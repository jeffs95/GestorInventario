<?php

namespace App\Http\Controllers;

use App\Models\Talla;
use App\Models\Zapato;
use App\Models\ZapatoFoto;
use App\Models\ZapatoLote;
use App\Traits\FuncionesDigitalizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PreparacionController extends Controller
{
    use FuncionesDigitalizacion;

    /**
     * Iniciar preparación: pre-genera TODOS los códigos del lote de una vez
     * (estado = 'pendiente_precio') y redirige a la hoja de barcodes para imprimir.
     */
    public function iniciar(ZapatoLote $zapato_lote)
    {
        if ($zapato_lote->estado !== 'contado') {
            return back()->with('warning', 'Este lote ya fue iniciado o completado.');
        }

        $zapato_lote->load('apertura.lote');
        $ref = "ZL{$zapato_lote->id}";

        DB::transaction(function () use ($zapato_lote, $ref) {
            for ($i = 0; $i < $zapato_lote->cantidad_contada; $i++) {
                Zapato::create([
                    'codigo_unico'   => Zapato::generarCodigo($ref, $zapato_lote->clasificacion),
                    'zapato_lote_id' => $zapato_lote->id,
                    'apertura_id'    => $zapato_lote->apertura_id,
                    'sucursal_id'    => $zapato_lote->apertura->lote->sucursal_destino_id,
                    'categoria_id'   => $zapato_lote->categoria_id,
                    'tipo_id'        => $zapato_lote->tipo_id,
                    'clasificacion'  => $zapato_lote->clasificacion,
                    'precio_lista'   => 0.00,
                    'estado'         => 'pendiente_precio',
                    'usuario_id'     => auth()->id(),
                ]);
            }

            $zapato_lote->update(['estado' => 'en_preparacion']);
        });

        return redirect()->route('preparacion.barcodes', $zapato_lote)
            ->with('success', "{$zapato_lote->cantidad_contada} códigos generados. Imprime la hoja y pégalos en los zapatos.");
    }

    /**
     * Hoja de barcodes para imprimir (todos los pendiente_precio del lote).
     */
    public function barcodes(ZapatoLote $zapato_lote)
    {
        $zapato_lote->load([
            'apertura.lote.proveedor',
            'categoria',
            'tipo',
            'zapatos' => fn ($q) => $q->orderBy('id'),
        ]);

        return view('preparacion.barcodes', compact('zapato_lote'));
    }

    /**
     * Pantalla de asignación de precios:
     * escanea barcode → ingresa precio → siguiente.
     */
    public function show(ZapatoLote $zapato_lote)
    {
        $zapato_lote->load([
            'apertura.lote.proveedor',
            'apertura.lote.sucursalDestino',
            'categoria',
            'tipo',
            'zapatos' => fn ($q) => $q->with('talla')->orderBy('id'),
        ]);

        $tallas = Talla::where('activo', true)->orderBy('nombre')->get();

        return view('preparacion.show', compact('zapato_lote', 'tallas'));
    }

    /**
     * Asignar precio (y detalles opcionales) a un zapato pre-generado.
     * Recibe 'codigo_unico' escaneado + 'precio_lista' y actualiza el registro.
     */
    public function store(Request $request, ZapatoLote $zapato_lote)
    {
        if ($zapato_lote->estado === 'completado') {
            return back()->with('warning', 'Este lote ya está completado.')->withInput();
        }

        if ($zapato_lote->cantidad_registrada >= $zapato_lote->cantidad_contada) {
            return back()->with('warning', 'Ya se asignaron precios a todos los zapatos de este lote.')->withInput();
        }

        $data = $request->validate([
            'codigo_unico' => 'required|string|max:50',
            'precio_lista' => 'required|numeric|min:0.01',
            'fotos'        => 'required|array|min:1',
            'fotos.*'      => 'required|file|image|max:8192',
            'talla_id'     => 'nullable|exists:talla,id',
            'color'        => 'nullable|string|max:50',
            'marca'        => 'nullable|string|max:80',
            'genero'       => 'nullable|in:hombre,mujer,nino,nina,unisex',
            'condicion'    => 'nullable|in:muy_bueno,bueno,regular',
            'notas'        => 'nullable|string|max:255',
        ]);

        // Localizar el zapato pre-generado dentro de este lote
        $zapato = Zapato::where('codigo_unico', $data['codigo_unico'])
            ->where('zapato_lote_id', $zapato_lote->id)
            ->where('estado', 'pendiente_precio')
            ->first();

        if (! $zapato) {
            return back()
                ->withInput()
                ->with('error', "Código «{$data['codigo_unico']}» no encontrado en este lote o ya tiene precio asignado.");
        }

        // Actualizar el zapato con precio y detalles
        $zapato->update([
            'precio_lista' => $data['precio_lista'],
            'talla_id'     => $data['talla_id'] ?? null,
            'color'        => $data['color']     ?? null,
            'marca'        => $data['marca']     ?? null,
            'genero'       => $data['genero']    ?? null,
            'condicion'    => $data['condicion'] ?? null,
            'notas'        => $data['notas']     ?? null,
            'estado'       => 'en_inventario',
        ]);

        // ── Subir fotos al FTP ───────────────────────────────────────────────
        $subCarpeta  = strtoupper(str_replace('_', '-', $zapato_lote->clasificacion));
        $fotosSubidas = [];
        $erroresFoto  = [];

        foreach ($data['fotos'] as $idx => $archivoFoto) {
            if (! $archivoFoto->isValid()) {
                $erroresFoto[] = "Foto " . ($idx + 1) . ": archivo inválido.";
                continue;
            }
            try {
                $path = $this->subir_foto_zapato($archivoFoto, $zapato->id, $subCarpeta);
                $fotosSubidas[] = ['path' => $path, 'orden' => $idx];
            } catch (\Exception $e) {
                Log::error('PreparacionController@store — FTP foto upload failed', [
                    'zapato_id'   => $zapato->id,
                    'zapato_lote' => $zapato_lote->id,
                    'foto_idx'    => $idx,
                    'error'       => $e->getMessage(),
                ]);
                $erroresFoto[] = "Foto " . ($idx + 1) . ": " . $e->getMessage();
            }
        }

        if (! empty($fotosSubidas)) {
            // La primera foto va en foto_path del zapato (compatibilidad)
            $zapato->update(['foto_path' => $fotosSubidas[0]['path']]);

            // Todas las fotos van en la tabla zapato_fotos
            ZapatoFoto::where('zapato_id', $zapato->id)->delete(); // limpiar si ya había
            foreach ($fotosSubidas as $f) {
                ZapatoFoto::create([
                    'zapato_id' => $zapato->id,
                    'foto_path' => $f['path'],
                    'orden'     => $f['orden'],
                ]);
            }
        }

        if (! empty($erroresFoto)) {
            session()->flash('warning', 'Precio asignado pero ' . implode('; ', $erroresFoto));
        }

        // Incrementar contador del lote
        $zapato_lote->increment('cantidad_registrada');
        $zapato_lote->refresh();

        // Auto-completar cuando ya se asignaron todos los precios
        if ($zapato_lote->cantidad_registrada >= $zapato_lote->cantidad_contada) {
            $zapato_lote->update(['estado' => 'completado']);
            return redirect()->route('preparacion.show', $zapato_lote)
                ->with('success', '¡Lote completado! Todos los zapatos están en inventario.')
                ->with('ultimo_codigo', $zapato->codigo_unico);
        }

        $pendientes = $zapato_lote->cantidad_contada - $zapato_lote->cantidad_registrada;

        return redirect()->route('preparacion.show', $zapato_lote)
            ->with('success', "Precio asignado a {$zapato->codigo_unico}. Faltan {$pendientes}.")
            ->with('ultimo_codigo', $zapato->codigo_unico);
    }
}
