<!-- BEGIN: Main Menu-->

<div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item me-auto">
                <span class="brand-logo">
                    <img src="./img/elparaiso.png"
                         alt="Login"
                         style="width: 100%; height: 100%; object-fit: cover;" />
                </span>
            </li>
        </ul>
    </div>

    <div class="shadow-bottom"></div>

    <div class="main-menu-content">
        <ul class="navigation navigation-main"
            id="main-menu-navigation"
            data-menu="menu-navigation">

            <li class="navigation-header">
                <span data-i18n="Modules">Módulos</span>

                <svg xmlns="http://w3.org"
                     width="14"
                     height="14"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round"
                     class="feather feather-more-horizontal">

                    <circle cx="12" cy="12" r="1"></circle>
                    <circle cx="19" cy="12" r="1"></circle>
                    <circle cx="5" cy="12" r="1"></circle>

                </svg>
            </li>

            <?php

            $html = '';

            // Params para el menú seleccionado
            $selected_menu = '';
            $selected_sub_menu = '';

            // Variables temporales para separar los subgrupos de Ventas
            $ventas_subgroups = [];
            $main_modules = [];

            // Clasificamos los módulos
            foreach ($params['menu']['modules'] as $item) {

                $id_m = isset($item['id'])
                    ? (int)$item['id']
                    : (isset($item['id_menu']) ? (int)$item['id_menu'] : 0);

                // Restaurante(10), Restobar(11), Room Service(12), POS(13)
                if (in_array($id_m, [10, 11, 12, 13])) {

                    $ventas_subgroups[] = $item;

                } else {

                    $main_modules[] = $item;
                }
            }

            // Recorremos módulos principales
            foreach ($main_modules as $item) {

                $html .= '<li class="nav-item">
                    <a class="d-flex align-items-center">
                        <i data-feather="' . $item["icon"] . '"></i>
                        <span class="menu-title text-truncate">'
                            . $item["description"] .
                        '</span>
                    </a>';

                $html .= '<ul class="menu-content">';

                $id_actual = isset($item['id'])
                    ? (int)$item['id']
                    : (isset($item['id_menu']) ? (int)$item['id_menu'] : 0);

                // Módulo Ventas
                if ($id_actual === 4) {

                    // Opciones propias de Ventas (Facturación, Nota Crédito, Registro, Lista de ventas...)
                    foreach ($item['sub_menu'] as $row) {

                        $menu_url = $row["url"];
                        $active = ($menu_url == $params['menu']['view']);

                        if ($active) {
                            $selected_menu = $item['description'];
                            $selected_sub_menu = $row['description'];
                        }

                        $html .= '<li class="' . ($active ? 'active' : '') . '">
                            <a class="d-flex align-items-center"
                               href="' . BASE_URL . $menu_url . '">
                                <i data-feather="' . $row["icon"] . '"></i>
                                <span class="menu-item text-truncate">'
                                    . $row["description"] .
                                '</span>
                            </a>
                        </li>';
                    }

                    // Subgrupos de Ventas
                    foreach ($ventas_subgroups as $subgroup) {

                        $html .= '<li class="nav-item">
                            <a class="d-flex align-items-center">
                                <i data-feather="' . $subgroup["icon"] . '"></i>
                                <span class="menu-item text-truncate"
                                      style="font-weight: bold; padding-left: 10px;">'
                                    . $subgroup["description"] .
                                '</span>
                            </a>';

                        $html .= '<ul class="menu-content" style="margin-left: 15px;">';

                        // Submenús
                        foreach ($subgroup['sub_menu'] as $row) {

                            /*
                             * URL ORIGINAL
                             * Se mantiene para Restaurante, Restobar y POS.
                             */
                            $menu_url = $row["url"];

                            /*
                             * SOLO ROOM SERVICE
                             * IDs 55 al 59.
                             */
                            if (
                                $subgroup['description'] === 'Room Service'
                            ) {

                                switch ((int)$row["id"]) {

                                    case 55:
                                        $menu_url = 'RoomService/NuevoPedido';
                                        break;

                                    case 56:
                                        $menu_url = 'RoomService/PedidosPendientes';
                                        break;

                                    case 57:
                                        $menu_url = 'RoomService/PedidosPreparacion';
                                        break;

                                    case 58:
                                        $menu_url = 'RoomService/PedidosEntregados';
                                        break;

                                    case 59:
                                        $menu_url = 'RoomService/CargoHabitacion';
                                        break;
                                }
                            }

                            // Menú seleccionado
                            if (
                                $menu_url == $params['menu']['view']
                            ) {

                                $selected_menu =
                                    $subgroup['description'];

                                $selected_sub_menu =
                                    $row['description'];

                                $html .= '<li class="active">
                                    <a class="d-flex align-items-center"
                                       href="' . BASE_URL . $menu_url . '">

                                        <i data-feather="' . $row["icon"] . '"></i>

                                        <span class="menu-item text-truncate">'
                                            . $row["description"] .
                                        '</span>

                                    </a>
                                </li>';

                            } else {

                                $html .= '<li class="">
                                    <a class="d-flex align-items-center"
                                       href="' . BASE_URL . $menu_url . '">

                                        <i data-feather="' . $row["icon"] . '"></i>

                                        <span class="menu-item text-truncate">'
                                            . $row["description"] .
                                        '</span>

                                    </a>
                                </li>';
                            }
                        }

                        $html .= '</ul></li>';
                    }

                } else {

                    // Comportamiento normal para módulos fuera de Ventas
                    foreach ($item['sub_menu'] as $row) {

                        $menu_url = $row["url"];

                        if (
                            $menu_url == $params['menu']['view']
                        ) {

                            $selected_menu =
                                $item['description'];

                            $selected_sub_menu =
                                $row['description'];

                            $html .= '<li class="active">
                                <a class="d-flex align-items-center"
                                   href="' . BASE_URL . $menu_url . '">

                                    <i data-feather="' . $row["icon"] . '"></i>

                                    <span class="menu-item text-truncate">'
                                        . $row["description"] .
                                    '</span>

                                </a>
                            </li>';

                        } else {

                            $html .= '<li class="">
                                <a class="d-flex align-items-center"
                                   href="' . BASE_URL . $menu_url . '">

                                    <i data-feather="' . $row["icon"] . '"></i>

                                    <span class="menu-item text-truncate">'
                                        . $row["description"] .
                                    '</span>

                                </a>
                            </li>';
                        }
                    }
                }

                $html .= '</ul>';
                $html .= '</li>';
            }

            echo $html;

            ?>

        </ul>
    </div>
</div>

<!-- END: Main Menu-->