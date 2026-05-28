<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait FuncionesDigitalizacion
{
    // ── Disco FTP configurado en config/filesystems.php ─────────────────────
    protected string $diskFtp = 'ftp_documents';

    // ────────────────────────────────────────────────────────────────────────
    // SUBIDA DESDE BASE64 (uso general)
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Guarda un archivo en el FTP a partir de un string base64 o data-URL.
     *
     * @param  int         $identificador  ID del registro relacionado (zapato, lote, etc.)
     * @param  string      $carpeta        Carpeta principal  (ej: 'ZAPATOS')
     * @param  string      $raw            Contenido base64 o data:image/...;base64,...
     * @param  string      $sub_carpeta    Sub-carpeta        (ej: 'PRIMERA_LAVADO')
     * @param  string|null $filename       Nombre sin extensión; null = auto-generado
     * @return string                      Ruta relativa guardada en el FTP
     */
    public function escribir_ftp(
        int $identificador,
        string $carpeta,
        string $raw,
        string $sub_carpeta,
        ?string $filename = null
    ): string {
        // Separar el header del data-URL si viene así
        if (str_starts_with($raw, 'data:')) {
            $partes = explode('base64,', $raw, 2);
            $raw    = $partes[1] ?? $raw;
        }

        $file = base64_decode($raw, true);
        if ($file === false) {
            throw new \Exception('ERROR: base64 inválido — no se pudo obtener el binario.');
        }

        // Detectar extensión por MIME
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $file);
        finfo_close($finfo);

        $mimeToExt = [
            'application/pdf'                                                         => 'pdf',
            'image/jpeg'                                                              => 'jpg',
            'image/png'                                                               => 'png',
            'image/gif'                                                               => 'gif',
            'image/webp'                                                              => 'webp',
            'application/msword'                                                      => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel'                                                => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'       => 'xlsx',
        ];

        $extension = $mimeToExt[$mimeType] ?? 'bin';
        $filename  = is_null($filename)
            ? date('dmYHis') . $identificador . '.' . $extension
            : "{$filename}.{$extension}";

        $path = "PACA_MANAGER/{$carpeta}/{$sub_carpeta}/{$filename}";

        if (! Storage::disk($this->diskFtp)->put($path, $file)) {
            throw new \Exception('Error al subir el archivo al servidor FTP.');
        }

        return $path;
    }

    // ────────────────────────────────────────────────────────────────────────
    // SUBIDA DESDE ARCHIVO DE FORMULARIO (UploadedFile)
    // ────────────────────────────────────────────────────────────────────────

    /**
     * Sube la foto de un zapato al FTP directamente desde el input file del formulario.
     *
     * @param  UploadedFile $file        Archivo recibido del request
     * @param  int          $zapatoId    ID del zapato (para nombrar el archivo)
     * @param  string       $subCarpeta  Sub-carpeta dentro de ZAPATOS (ej: 'PRIMERA_LAVADO')
     * @return string                    Ruta relativa (se guarda en foto_path)
     */
    public function subir_foto_zapato(UploadedFile $file, int $zapatoId, string $subCarpeta = 'GENERAL'): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename  = "zapato_{$zapatoId}_" . time() . '.' . $extension;
        $path      = "PACA_MANAGER/ZAPATOS/{$subCarpeta}/{$filename}";

        if (! Storage::disk($this->diskFtp)->put($path, $file->getContent())) {
            throw new \Exception('Error al subir la foto al servidor FTP.');
        }

        return $path;
    }

    // ────────────────────────────────────────────────────────────────────────
    // LECTURA / STREAMING
    // ────────────────────────────────────────────────────────────────────────

    /** Devuelve true si el archivo existe en el FTP */
    public function existe_ftp(string $path): bool
    {
        return Storage::disk($this->diskFtp)->exists($path);
    }

    /**
     * Hace streaming del archivo del FTP como respuesta HTTP
     * (ideal para mostrar fotos inline desde rutas privadas).
     */
    public function leer_ftp(string $path, string $nombreArchivo, string $mimeType = 'jpg')
    {
        $disk = Storage::disk($this->diskFtp);

        if (! $disk->exists($path)) {
            abort(404, 'El archivo no existe en el servidor FTP.');
        }

        $mimeMap = [
            'pdf'             => 'application/pdf',
            'application/pdf' => 'application/pdf',
            'jpg'             => 'image/jpeg',
            'jpeg'            => 'image/jpeg',
            'image/jpeg'      => 'image/jpeg',
            'png'             => 'image/png',
            'image/png'       => 'image/png',
            'webp'            => 'image/webp',
            'image/webp'      => 'image/webp',
        ];

        $resolvedMime = $mimeMap[$mimeType] ?? 'application/octet-stream';

        return response()->stream(function () use ($disk, $path) {
            $stream = $disk->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type'        => $resolvedMime,
            'Content-Disposition' => 'inline; filename="' . str_replace(' ', '_', $nombreArchivo) . '"',
        ]);
    }

    /** Devuelve un stream del archivo (para procesamiento interno) */
    public function obtener_contenido_ftp(string $path)
    {
        return Storage::disk($this->diskFtp)->readStream($path);
    }

    /** Elimina un archivo del FTP. Devuelve true si se eliminó o no existía */
    public function eliminar_ftp(string $path): bool
    {
        $disk = Storage::disk($this->diskFtp);
        return ! $disk->exists($path) || $disk->delete($path);
    }
}
