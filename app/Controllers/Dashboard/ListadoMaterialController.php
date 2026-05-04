<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\ListadoMaterial;
use App\Models\ListadoMaterialItem;
use App\Models\Cliente;
use App\Models\Proyecto;
use App\Models\ModuloDetalle;

class ListadoMaterialController extends BaseController
{
    protected $listadoMaterialModel;
    protected $listadoMaterialItemModel;
    protected $clienteModel;
    protected $proyectoModel;

    public function __construct()
    {
        $this->listadoMaterialModel = new ListadoMaterial();
        $this->listadoMaterialItemModel = new ListadoMaterialItem();
        $this->clienteModel = new Cliente();
        $this->proyectoModel = new Proyecto();
    }

    /**
     * Vista principal de lista
     */
    public function lista()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        return view('Modulos/listado_material/lista', $data);
    }

    /**
     * Vista de registro
     */
    public function registro()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;
        
        $data['clientes'] = $this->clienteModel->where('estado', 'A')->findAll();
        $data['proyectos'] = $this->proyectoModel->findAll();

        return view('Modulos/listado_material/registro', $data);
    }

    /**
     * Obtener listados para DataTable
     */
    public function getListadosMateriales()
    {
        $busqueda = $this->request->getGet('search')['value'] ?? '';
        $clienteId = $this->request->getGet('cliente_id') ?? '';
        $estado = $this->request->getGet('estado') ?? '';

        $query = $this->listadoMaterialModel
            ->select('listado_material.*, 
                      c.nombre_razon_social as cliente_nombre,
                      p.nombre as proyecto_nombre')
            ->join('clientes c', 'c.id = listado_material.cliente_id', 'left')
            ->join('proyectos p', 'p.id = listado_material.proyecto_id', 'left');

        if (!empty($clienteId)) {
            $query->where('listado_material.cliente_id', $clienteId);
        }

        if (!empty($estado)) {
            $query->where('listado_material.estado', $estado);
        }

        if (!empty($busqueda)) {
            $query->groupStart()
                  ->like('listado_material.numero_listado', $busqueda)
                  ->orLike('listado_material.titulo', $busqueda)
                  ->orLike('c.nombre_razon_social', $busqueda)
                  ->groupEnd();
        }

        $rows = $query->orderBy('listado_material.fecha_listado', 'DESC')
                     ->findAll();

        $data = [];
        foreach ($rows as $r) {
            $estadoBadge = match ($r->estado ?? 'borrador') {
                'borrador' => '<span class="badge badge-secondary">Borrador</span>',
                'finalizado' => '<span class="badge badge-success">Finalizado</span>',
                'enviado' => '<span class="badge badge-info">Enviado</span>',
                'archivado' => '<span class="badge badge-warning">Archivado</span>',
                default => '<span class="badge badge-secondary">N/A</span>',
            };

            $acciones = '
                <a href="' . base_url('dashboard/listado-material/detalle/' . $r->id) . '" class="btn btn-sm btn-info" title="Ver detalle">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="' . base_url('dashboard/listado-material/editar/' . $r->id) . '" class="btn btn-sm btn-warning" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="' . base_url('dashboard/listado-material/generarPDF/' . $r->id) . '" class="btn btn-sm btn-danger" title="Generar PDF">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <button class="btn btn-sm btn-danger" onclick="eliminarListado(' . $r->id . ')" title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            ';

            $data[] = [
                $r->numero_listado,
                esc($r->titulo),
                esc($r->cliente_nombre ?? 'N/A'),
                esc($r->proyecto_nombre ?? 'N/A'),
                date('d/m/Y', strtotime($r->fecha_listado ?? $r->created_at)),
                $estadoBadge,
                $acciones
            ];
        }

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    /**
     * Registrar nuevo listado
     */
    public function registrar()
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $rules = [
            'titulo' => 'required|min_length[3]|max_length[200]',
            'fecha_listado' => 'required|valid_date',
            'cliente_id' => 'permit_empty|is_natural_no_zero',
            'proyecto_id' => 'permit_empty|is_natural_no_zero',
            'estado' => 'required|in_list[borrador,finalizado,enviado,archivado]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validar que haya al menos un material
        $materiales = $this->request->getPost('materiales') ?? [];
        $materialesValidos = array_filter($materiales, function($material) {
            return !empty($material['nombre_material']);
        });

        if (empty($materialesValidos)) {
            return redirect()->back()->withInput()->with('error', 'Debe agregar al menos un material al listado');
        }

        $dataListado = [
            'titulo' => $this->request->getPost('titulo'),
            'cliente_id' => $this->request->getPost('cliente_id') ?: null,
            'proyecto_id' => $this->request->getPost('proyecto_id') ?: null,
            'fecha_listado' => $this->request->getPost('fecha_listado'),
            'estado' => $this->request->getPost('estado'),
            'observaciones' => $this->request->getPost('observaciones'),
        ];

        $listadoId = $this->listadoMaterialModel->insert($dataListado);

        if (!$listadoId) {
            return redirect()->back()->withInput()->with('error', 'Error al crear el listado de materiales');
        }

        // Guardar items
        $this->guardarItems($listadoId, $materiales);

        return redirect()->to(base_url('dashboard/listado-material/detalle/' . $listadoId))
                        ->with('success', 'Listado de materiales creado exitosamente');
    }

    /**
     * Vista de edición
     */
    public function editar($id)
    {
        $listado = $this->listadoMaterialModel->find($id);

        if (!$listado) {
            return redirect()->to(base_url('dashboard/listado-material/lista'))
                           ->with('error', 'Listado no encontrado');
        }

        $items = $this->listadoMaterialItemModel->getItemsByListado($id);

        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;
        
        $data['listado'] = $listado;
        $data['items'] = $items;
        $data['clientes'] = $this->clienteModel->where('estado', 'A')->findAll();
        $data['proyectos'] = $this->proyectoModel->findAll();

        return view('Modulos/listado_material/editar', $data);
    }

    /**
     * Actualizar listado
     */
    public function update($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $rules = [
            'titulo' => 'required|min_length[3]|max_length[200]',
            'fecha_listado' => 'required|valid_date',
            'cliente_id' => 'permit_empty|is_natural_no_zero',
            'proyecto_id' => 'permit_empty|is_natural_no_zero',
            'estado' => 'required|in_list[borrador,finalizado,enviado,archivado]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validar que haya al menos un material
        $materiales = $this->request->getPost('materiales') ?? [];
        $materialesValidos = array_filter($materiales, function($material) {
            return !empty($material['nombre_material']);
        });

        if (empty($materialesValidos)) {
            return redirect()->back()->withInput()->with('error', 'Debe agregar al menos un material al listado');
        }

        $dataListado = [
            'titulo' => $this->request->getPost('titulo'),
            'cliente_id' => $this->request->getPost('cliente_id') ?: null,
            'proyecto_id' => $this->request->getPost('proyecto_id') ?: null,
            'fecha_listado' => $this->request->getPost('fecha_listado'),
            'estado' => $this->request->getPost('estado'),
            'observaciones' => $this->request->getPost('observaciones'),
        ];

        if (!$this->listadoMaterialModel->update($id, $dataListado)) {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el listado');
        }

        // Eliminar items antiguos y guardar nuevos
        $this->listadoMaterialItemModel->deleteByListado($id);
        $this->guardarItems($id, $materiales);

        return redirect()->to(base_url('dashboard/listado-material/detalle/' . $id))
                        ->with('success', 'Listado actualizado exitosamente');
    }

    /**
     * Ver detalle del listado
     */
    public function detalle($id)
    {
        $listado = $this->listadoMaterialModel->getListadoConRelaciones($id);

        if (!$listado) {
            return redirect()->to(base_url('dashboard/listado-material/lista'))
                           ->with('error', 'Listado no encontrado');
        }

        $items = $this->listadoMaterialItemModel->getItemsByListado($id);

        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;
        
        $data['listado'] = $listado;
        $data['items'] = $items;

        return view('Modulos/listado_material/detalle', $data);
    }

    /**
     * Eliminar listado
     */
    public function eliminar()
    {
        $id = $this->request->getPost('id');

        if (!$id) {
            return redirect()->back()->with('error', 'ID de listado requerido');
        }

        // Eliminar items primero (por CASCADE debería hacerse automático, pero por seguridad)
        $this->listadoMaterialItemModel->deleteByListado($id);

        if ($this->listadoMaterialModel->delete($id)) {
            return redirect()->to(base_url('dashboard/listado-material/lista'))
                           ->with('success', 'Listado eliminado exitosamente');
        }

        return redirect()->back()->with('error', 'Error al eliminar el listado');
    }

    /**
     * Generar PDF
     */
    public function generarPDF($id)
    {
        log_message('debug', 'Iniciando generación de PDF para listado ID: ' . $id);

        $listado = $this->listadoMaterialModel->getListadoConRelaciones($id);

        if (!$listado) {
            log_message('error', 'Listado no encontrado: ' . $id);
            return redirect()->back()->with('error', 'Listado no encontrado');
        }

        $items = $this->listadoMaterialItemModel->getItemsByListado($id);

        log_message('debug', 'Items encontrados: ' . count($items));

        try {
            log_message('debug', 'Creando instancia de DomPDF');
            
            // Usar DomPDF directamente
            $dompdf = new \Dompdf\Dompdf();
            $html = $this->generarHTMLListado($listado, $items);
            
            log_message('debug', 'HTML generado, longitud: ' . strlen($html) . ' caracteres');
            log_message('debug', 'Cargando HTML en DomPDF');
            $dompdf->loadHtml($html);
            
            log_message('debug', 'Configurando papel A4 portrait');
            $dompdf->setPaper('A4', 'portrait');
            
            log_message('debug', 'Renderizando PDF');
            $dompdf->render();
            
            log_message('debug', 'Obteniendo output del PDF');
            $pdfContent = $dompdf->output();
            log_message('debug', 'PDF generado exitosamente, tamaño: ' . strlen($pdfContent) . ' bytes');
            
            $filename = 'Listado_Material_' . $listado->numero_listado . '.pdf';
            log_message('debug', 'Nombre del archivo: ' . $filename);

            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($pdfContent);

        } catch (\Exception $e) {
            log_message('error', 'Error al generar PDF: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }

    /**
     * Obtener clientes para select
     */
    public function getClientesSelect()
    {
        $clientes = $this->clienteModel->where('estado', 'A')
                                      ->orderBy('nombre_razon_social', 'ASC')
                                      ->findAll();

        $data = [];
        foreach ($clientes as $cliente) {
            $data[] = [
                'id' => $cliente->id,
                'text' => $cliente->nombre_razon_social
            ];
        }

        return $this->response->setJSON($data);
    }

    /**
     * Obtener proyectos para select por cliente
     */
    public function getProyectosByCliente()
    {
        $clienteId = $this->request->getGet('cliente_id');

        if (!$clienteId) {
            return $this->response->setJSON([]);
        }

        $proyectos = $this->proyectoModel->where('cliente_id', $clienteId)
                                        ->orderBy('nombre', 'ASC')
                                        ->findAll();

        $data = [];
        foreach ($proyectos as $proyecto) {
            $data[] = [
                'id' => $proyecto->id,
                'text' => $proyecto->nombre
            ];
        }

        return $this->response->setJSON($data);
    }

    /**
     * Guardar items del listado
     */
    private function guardarItems($listadoId, $materiales)
    {
        if (empty($materiales)) {
            return;
        }

        foreach ($materiales as $index => $material) {
            if (empty($material['nombre_material'])) {
                continue;
            }

            $dataItem = [
                'listado_material_id' => $listadoId,
                'nombre_material' => $material['nombre_material'],
                'descripcion' => $material['descripcion'] ?? null,
                'unidad_medida' => !empty($material['unidad_medida']) ? $material['unidad_medida'] : null,
                'cantidad' => !empty($material['cantidad']) ? $material['cantidad'] : null,
                'orden' => $index + 1,
            ];

            $this->listadoMaterialItemModel->insert($dataItem);
        }
    }

    /**
     * Generar HTML para PDF (mismo formato que cotizaciones)
     */
    private function generarHTMLListado($listado, $items)
    {
        log_message('debug', 'Iniciando generación de HTML para PDF');

        // Obtener datos de la empresa
        $empresaModel = new \App\Models\Empresa();
        $empresa = $empresaModel->getDatosParaPDF();

        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Listado de Materiales - ' . $listado->numero_listado . '</title>
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
                .items-table .quantity { text-align: center; width: 100px; }
                .items-table .unit { text-align: center; width: 100px; }
                .material-nombre { color: #333; line-height: 1.5; }
                .footer { margin-top: 40px; font-size: 10px; color: #666; }
                .observaciones { margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; }
                .observaciones h3 { color: #856404; margin-bottom: 10px; font-size: 14px; }
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
                <div class="quote-title">LISTADO DE MATERIALES ' . esc($listado->numero_listado) . '</div>
                <div class="quote-details">
                    <div class="left">
                        <strong>Título:</strong> ' . esc($listado->titulo) . '<br>
                        <strong>Fecha:</strong> ' . date('d/m/Y', strtotime($listado->fecha_listado ?? $listado->created_at)) . '<br>
                        <strong>Estado:</strong> ' . ucfirst($listado->estado) . '
                    </div>
                    <div class="right">
                        <strong>Número:</strong> ' . esc($listado->numero_listado) . '
                    </div>
                </div>
            </div>

            <div class="client-info">
                <div class="client-title">INFORMACIÓN DEL CLIENTE</div>
                <strong>' . esc($listado->cliente_nombre ?? 'No especificado') . '</strong><br>';
        
        if (!empty($listado->proyecto_nombre)) {
            $html .= '<strong>Proyecto:</strong> ' . esc($listado->proyecto_nombre) . '<br>';
        }
        
        $html .= '
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th class="number">#</th>
                        <th>Material</th>
                        <th class="unit">Unidad</th>
                        <th class="quantity">Cantidad</th>
                    </tr>
                </thead>
                <tbody>';

        if (empty($items)) {
            $html .= '
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: #999;">
                            No hay materiales registrados
                        </td>
                    </tr>';
        } else {
            $contador = 1;
            foreach ($items as $item) {
                $html .= '
                    <tr>
                        <td class="number">' . $contador . '</td>
                        <td>
                            <div class="material-nombre">' . nl2br(esc($item->nombre_material)) . '</div>
                        </td>
                        <td class="unit">' . esc($item->unidad_medida ?? '-') . '</td>
                        <td class="quantity">' . ($item->cantidad !== null ? number_format($item->cantidad, 2, ',', '.') : '-') . '</td>
                    </tr>';
                $contador++;
            }
        }

        $html .= '
                </tbody>
            </table>';

        if (!empty($listado->observaciones)) {
            $html .= '
            <div class="observaciones">
                <h3>Observaciones</h3>
                <p>' . nl2br(esc($listado->observaciones)) . '</p>
            </div>';
        }

        $html .= '
            <div class="footer">
                <p><strong>Gracias por confiar en nuestros servicios.</strong></p>
                <p>Este listado fue generado el ' . date('d/m/Y H:i') . '</p>
                <p>Para cualquier consulta, no dude en contactarnos.</p>
            </div>
        </body>
        </html>';

        log_message('debug', 'HTML generado, longitud: ' . strlen($html) . ' caracteres');

        return $html;
    }
}

