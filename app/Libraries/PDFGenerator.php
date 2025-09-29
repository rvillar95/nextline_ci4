<?php

namespace App\Libraries;

use CodeIgniter\HTTP\ResponseInterface;

class PDFGenerator
{
    protected $response;
    protected $dompdf;

    public function __construct()
    {
        log_message('debug', 'Inicializando PDFGenerator');
        $this->response = service('response');
        
        // Verificar si DomPDF está disponible
        if (!class_exists('\Dompdf\Dompdf')) {
            log_message('error', 'DomPDF no está instalado');
            throw new \Exception('DomPDF no está instalado. Ejecuta: composer require dompdf/dompdf');
        }
        
        log_message('debug', 'DomPDF encontrado, creando instancia');
        $this->dompdf = new \Dompdf\Dompdf();
        log_message('debug', 'PDFGenerator inicializado correctamente');
    }

    /**
     * Generar PDF de cotización
     */
    public function generarCotizacionPDF($cotizacion, $items = [], $archivos = [])
    {
        log_message('debug', 'Iniciando generación de HTML para PDF');
        log_message('debug', 'Datos de cotización recibidos: ' . json_encode([
            'id' => $cotizacion->id ?? 'N/A',
            'numero' => $cotizacion->numero_cotizacion ?? 'N/A',
            'titulo' => $cotizacion->proyecto_nombre ?? $cotizacion->titulo ?? 'N/A'
        ]));
        
        $html = $this->generarHTMLCotizacion($cotizacion, $items, $archivos);
        log_message('debug', 'HTML generado, longitud: ' . strlen($html) . ' caracteres');
        
        log_message('debug', 'Cargando HTML en DomPDF');
        $this->dompdf->loadHtml($html);
        
        log_message('debug', 'Configurando papel A4 portrait');
        $this->dompdf->setPaper('A4', 'portrait');
        
        log_message('debug', 'Renderizando PDF');
        $this->dompdf->render();
        
        log_message('debug', 'Obteniendo output del PDF');
        $output = $this->dompdf->output();
        log_message('debug', 'PDF generado exitosamente, tamaño: ' . strlen($output) . ' bytes');
        
        return $output;
    }

    /**
     * Generar HTML para la cotización
     */
    protected function generarHTMLCotizacion($cotizacion, $items, $archivos)
    {
        $fecha_cotizacion = date('d/m/Y', strtotime($cotizacion->fecha_cotizacion));
        $fecha_validez = $cotizacion->fecha_validez ? date('d/m/Y', strtotime($cotizacion->fecha_validez)) : 'No especificada';
        
        // Obtener datos de la empresa
        $empresaModel = new \App\Models\Empresa();
        $empresa = $empresaModel->getDatosParaPDF();
        

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Cotización ' . esc($cotizacion->numero_cotizacion) . '</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; margin: 0; padding: 20px; color: #1d2844; }
                .header { position: relative; margin-bottom: 30px; border-bottom: 2px solid #f0841a; padding-bottom: 20px; }
                .logo { position: absolute; top: 0; left: 0; max-width: 80px; height: auto; }
                .header-content { text-align: center; }
                .company-name { font-size: 24px; font-weight: bold; color: #1d2844; margin-bottom: 5px; }
                .company-info { font-size: 10px; color: #666; }
                .quote-info { margin-bottom: 30px; }
                .quote-title { font-size: 18px; font-weight: bold; margin-bottom: 10px; color: #1d2844; }
                .quote-details { display: table; width: 100%; }
                .quote-details .left, .quote-details .right { display: table-cell; width: 50%; vertical-align: top; }
                .client-info { background: #f3d7b0; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                .client-title { font-weight: bold; margin-bottom: 10px; color: #1d2844; }
                .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .items-table th { background: #f3d7b0; font-weight: bold; color: #1d2844; }
                .items-table .number { text-align: center; width: 50px; }
                .items-table .quantity { text-align: center; width: 80px; }
                .items-table .price, .items-table .total { text-align: right; width: 100px; }
                .totals { margin-top: 20px; }
                .totals-table { width: 300px; margin-left: auto; }
                .totals-table td { padding: 5px; }
                .totals-table .label { font-weight: bold; }
                .totals-table .amount { text-align: right; }
                .totals-table .total-row { border-top: 2px solid #f0841a; font-weight: bold; font-size: 14px; color: #1d2844; }
                .footer { margin-top: 40px; font-size: 10px; color: #666; }
                .project-description { page-break-inside: avoid; break-inside: avoid; margin-bottom: 20px; }
                .project-description h3 { page-break-after: avoid; break-after: avoid; }
                .project-description p { page-break-inside: avoid; break-inside: avoid; }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="data:image/png;base64,' . base64_encode(file_get_contents(FCPATH . $empresa['logo_path'])) . '" alt="' . esc($empresa['nombre']) . '" class="logo">
                <div class="header-content">
                    <div class="company-name">' . esc($empresa['nombre']) . '</div>
                    <div class="company-info">
                        ' . esc($empresa['descripcion']) . '<br>
                        Email: ' . esc($empresa['email']) . ' | Teléfono: ' . esc($empresa['telefono']) . '<br>
                        Dirección: ' . esc($empresa['direccion']) . '
                    </div>
                </div>
            </div>

            <div class="quote-info">
                <div class="quote-title">COTIZACIÓN ' . esc($cotizacion->numero_cotizacion) . '</div>
                <div class="quote-details">
                    <div class="left">
                        <strong>Título:</strong> ' . esc($cotizacion->proyecto_nombre ?? $cotizacion->titulo ?? 'Sin título') . '<br>
                        <strong>Fecha:</strong> ' . $fecha_cotizacion . '<br>
                        <strong>Válida hasta:</strong> ' . $fecha_validez . '
                    </div>
                    <div class="right">
                        <strong>Número:</strong> ' . esc($cotizacion->numero_cotizacion) . '
                    </div>
                </div>
            </div>

            <div class="client-info">
                <div class="client-title">INFORMACIÓN DEL CLIENTE</div>
                <strong>' . esc($cotizacion->cliente_nombre) . '</strong><br>';
        
        if ($cotizacion->contacto_nombre) {
            $html .= 'Contacto: ' . esc($cotizacion->contacto_nombre) . '<br>';
        }
        if ($cotizacion->telefono) {
            $html .= 'Teléfono: ' . esc($cotizacion->telefono) . '<br>';
        }
        if ($cotizacion->email) {
            $html .= 'Email: ' . esc($cotizacion->email) . '<br>';
        }
        if ($cotizacion->direccion) {
            $html .= 'Dirección: ' . esc($cotizacion->direccion) . '<br>';
        }
        if ($cotizacion->comuna && $cotizacion->region) {
            $html .= 'Ubicación: ' . esc($cotizacion->comuna) . ', ' . esc($cotizacion->region) . '<br>';
        }
        
        $html .= '
            </div>';

        if (!empty($items)) {
            $html .= '
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="number">#</th>
                        <th>Descripción</th>
                        <th class="quantity">Cantidad</th>
                        <th>Unidad</th>
                        <th class="price">Precio Unit.</th>
                        <th class="total">Subtotal</th>
                    </tr>
                </thead>
                <tbody>';
            
            $subtotal = 0;
            foreach ($items as $index => $item) {
                $subtotal += $item->subtotal;
                $html .= '
                    <tr>
                        <td class="number">' . ($index + 1) . '</td>
                        <td>' . esc($item->descripcion) . '</td>
                        <td class="quantity">' . number_format($item->cantidad, 0, ',', '.') . '</td>
                        <td>' . esc($item->unidad) . '</td>
                        <td class="price">$' . number_format($item->precio_unitario, 0, ',', '.') . '</td>
                        <td class="total">$' . number_format($item->subtotal, 0, ',', '.') . '</td>
                    </tr>';
            }
            
            $html .= '
                </tbody>
            </table>';
        }

        // Totales
        $subtotal_cotizacion = $cotizacion->subtotal_sin_iva ?? $cotizacion->subtotal ?? $subtotal ?? 0;
        $descuento_monto = $cotizacion->descuento_monto ?? 0;
        $iva_porcentaje = $cotizacion->iva_porcentaje ?? 19;
        $iva_monto = $cotizacion->iva_monto ?? ($subtotal_cotizacion * $iva_porcentaje / 100);
        $total_general = $cotizacion->total_general ?? ($subtotal_cotizacion - $descuento_monto + $iva_monto);

        $html .= '
            <div class="totals">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="amount">$' . number_format($subtotal_cotizacion, 0, ',', '.') . '</td>
                    </tr>';
        
        if ($descuento_monto > 0) {
            $html .= '
                    <tr>
                        <td class="label">Descuento:</td>
                        <td class="amount">-$' . number_format($descuento_monto, 0, ',', '.') . '</td>
                    </tr>';
        }
        
        $html .= '
                    <tr>
                        <td class="label">IVA (' . $iva_porcentaje . '%):</td>
                        <td class="amount">$' . number_format($iva_monto, 0, ',', '.') . '</td>
                    </tr>
                    <tr class="total-row">
                        <td class="label">TOTAL:</td>
                        <td class="amount">$' . number_format($total_general, 0, ',', '.') . '</td>
                    </tr>
                </table>
            </div>';

        if ($cotizacion->proyecto_descripcion ?? $cotizacion->descripcion) {
            $html .= '
            <div class="project-description" style="margin-top: 30px;">
                <h3>Descripción del Proyecto</h3>
                <p>' . nl2br(esc($cotizacion->proyecto_descripcion ?? $cotizacion->descripcion ?? '')) . '</p>
            </div>';
        }

        if ($cotizacion->condiciones_generales ?? $cotizacion->condiciones_pago) {
            $html .= '
            <div style="margin-top: 20px;">
                <h4>Condiciones de Pago</h4>
                <p>' . nl2br(esc($cotizacion->condiciones_generales ?? $cotizacion->condiciones_pago ?? '')) . '</p>
            </div>';
        }

        if ($cotizacion->observaciones_especiales ?? $cotizacion->observaciones) {
            $html .= '
            <div style="margin-top: 20px;">
                <h4>Observaciones</h4>
                <p>' . nl2br(esc($cotizacion->observaciones_especiales ?? $cotizacion->observaciones ?? '')) . '</p>
            </div>';
        }

        $html .= '
            <div class="footer">
                <p><strong>Gracias por confiar en nuestros servicios.</strong></p>
                <p>Esta cotización es válida por 30 días desde la fecha de emisión, salvo indicación contraria.</p>
                <p>Para cualquier consulta, no dude en contactarnos.</p>
            </div>
        </body>
        </html>';

        return $html;
    }

    /**
     * Descargar PDF
     */
    public function descargarPDF($filename, $pdfContent)
    {
        log_message('debug', 'Preparando descarga de PDF: ' . $filename);
        log_message('debug', 'Tamaño del contenido: ' . strlen($pdfContent) . ' bytes');
        
        $this->response->setHeader('Content-Type', 'application/pdf');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setBody($pdfContent);
        
        log_message('debug', 'Headers configurados, enviando respuesta');
        return $this->response;
    }

    /**
     * Mostrar PDF en navegador
     */
    public function mostrarPDF($pdfContent)
    {
        $this->response->setHeader('Content-Type', 'application/pdf');
        $this->response->setHeader('Content-Disposition', 'inline');
        $this->response->setBody($pdfContent);
        
        return $this->response;
    }
}
