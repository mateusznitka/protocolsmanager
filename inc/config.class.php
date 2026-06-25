<?php

class PluginProtocolsmanagerConfig extends CommonDBTM {

    static $rightname = 'plugin_protocolsmanager_config';

    static function getTypeName($nb = 0) {
        return 'Protocols Manager';
    }

    static function getMenuContent() {
        global $CFG_GLPI;
        $page = $CFG_GLPI['root_doc'] . '/plugins/protocolsmanager/front/config.form.php';
        return [
            'title' => self::getTypeName(1),
            'icon'  => 'ti ti-file-description',
            'page'  => $page,
            'links' => ['search' => $page],
        ];
    }

    function showFormProtocolsmanager() {
        if (self::checkRights()) {
            self::showMainPage();
        } else {
            global $CFG_GLPI;
            echo "<div align='center'><br><img src='" . $CFG_GLPI['root_doc'] . "/pics/warning.png'><br>" . __("Access denied") . "</div>";
        }
    }

    static function checkRights() {
        return Session::haveRight('plugin_protocolsmanager_config', READ);
    }

    static function showMainPage() {
        global $DB, $CFG_GLPI;
        $action = $CFG_GLPI['root_doc'] . '/plugins/protocolsmanager/front/config.form.php';

        echo '<div class="p-3">';

        // Nav-tabs
        echo '<div class="d-flex justify-content-between align-items-center mb-3">';
        echo '<ul class="nav nav-tabs flex-grow-1 mb-0 border-0" id="configTabs" role="tablist">';
        echo '<li class="nav-item" role="presentation">';
        echo '<button class="nav-link active" id="tab-templates-btn" data-bs-toggle="tab" data-bs-target="#tab-templates" type="button" role="tab">';
        echo __('Templates settings') . '</button></li>';
        echo '<li class="nav-item" role="presentation">';
        echo '<button class="nav-link" id="tab-email-btn" data-bs-toggle="tab" data-bs-target="#tab-email" type="button" role="tab">';
        echo __('Email settings') . '</button></li>';
        echo '</ul>';
        echo '<a href="https://github.com/mateusznitka/protocolsmanager/wiki" target="_blank" rel="noopener"'
            . ' class="btn btn-sm btn-outline-secondary ms-3 flex-shrink-0">'
            . '<i class="ti ti-help me-1"></i>Help</a>';
        echo '</div>';

        echo '<div class="tab-content">';

        // ── Tab: Report templates ──────────────────────────────────
        echo '<div class="tab-pane fade show active" id="tab-templates" role="tabpanel">';
        echo '<div class="card">';
        echo '<div class="card-header d-flex justify-content-between align-items-center">';
        echo '<span class="fw-bold">' . __('Report templates') . '</span>';
        echo '<button type="button" class="btn btn-sm btn-primary" id="btn-add-template"'
            . ' data-bs-toggle="modal" data-bs-target="#modal-template">'
            . '<i class="ti ti-plus me-1"></i>' . __('Add') . '</button>';
        echo '</div>';
        echo '<div class="card-body p-0">';
        self::showConfigs();
        echo '</div></div></div>';

        // ── Tab: Email settings ────────────────────────────────────
        echo '<div class="tab-pane fade" id="tab-email" role="tabpanel">';
        echo '<div class="card">';
        echo '<div class="card-header d-flex justify-content-between align-items-center">';
        echo '<span class="fw-bold">' . __('Email templates') . '</span>';
        echo '<button type="button" class="btn btn-sm btn-primary" id="btn-add-email"'
            . ' data-bs-toggle="modal" data-bs-target="#modal-email">'
            . '<i class="ti ti-plus me-1"></i>' . __('Add') . '</button>';
        echo '</div>';
        echo '<div class="card-body p-0">';
        self::showEmailConfigs();
        echo '</div></div></div>';

        echo '</div>'; // tab-content
        echo '<div class="text-end mt-3">';
        echo '<small class="text-muted">by <a href="https://mtnt.pl/blog/en/" target="_blank" rel="noopener" class="text-muted">Mateusz Nitka</a></small>';
        echo '</div>';
        echo '</div>'; // p-3

        self::renderTemplateModal($action);
        self::renderEmailModal($action);
        self::renderDeleteModal($action);

        $preview_url   = $CFG_GLPI['root_doc'] . '/plugins/protocolsmanager/front/preview.php';
        $token_url     = $CFG_GLPI['root_doc'] . '/plugins/protocolsmanager/front/csrf_token.php';

        self::renderScript($preview_url, $token_url);
    }

    static function showConfigs() {
        global $DB, $CFG_GLPI;
        $action = $CFG_GLPI['root_doc'] . '/plugins/protocolsmanager/front/config.form.php';

        $has_rows = false;
        $rows     = [];
        foreach ($DB->request(['FROM' => 'glpi_plugin_protocolsmanager_configs']) as $conf) {
            $rows[] = $conf;
        }

        if (empty($rows)) {
            echo '<div class="p-3 text-muted">' . __('No item found') . '</div>';
            return;
        }

        echo '<table class="table table-hover mb-0"><thead><tr>';
        echo '<th style="width:3rem">#</th>';
        echo '<th>' . __('Name') . '</th>';
        echo '<th>' . __('Font') . '</th>';
        echo '<th>' . __('Orientation') . '</th>';
        echo '<th>Email</th>';
        echo '<th style="width:9rem">' . __('Actions') . '</th>';
        echo '</tr></thead><tbody>';

        $i = 1;
        foreach ($rows as $conf) {
            $id             = (int) $conf['id'];
            $name           = htmlspecialchars($conf['name']           ?? '', ENT_QUOTES);
            $font           = htmlspecialchars($conf['font']           ?? '', ENT_QUOTES);
            $fontsize       = htmlspecialchars($conf['fontsize']       ?? '9', ENT_QUOTES);
            $header_color   = htmlspecialchars($conf['header_color']   ?? '#dee2e6', ENT_QUOTES);
            $breakword      = (int)($conf['breakword']      ?? 0);
            $city           = htmlspecialchars($conf['city']           ?? '', ENT_QUOTES);
            $orientation    = htmlspecialchars($conf['orientation']    ?? 'Portrait', ENT_QUOTES);
            $serial_mode    = (int)($conf['serial_mode']    ?? 1);
            $man_mode       = (int)($conf['man_mode']       ?? 1);
            $upper_content  = htmlspecialchars($conf['upper_content']  ?? '', ENT_QUOTES);
            $content        = htmlspecialchars($conf['content']        ?? '', ENT_QUOTES);
            $footer         = htmlspecialchars($conf['footer']         ?? '', ENT_QUOTES);
            $email_mode     = (int)($conf['email_mode']     ?? 2);
            $email_template = (int)($conf['email_template'] ?? 0);
            $logo           = htmlspecialchars($conf['logo']           ?? '', ENT_QUOTES);
            $is_default     = (int)($conf['is_default']     ?? 0);
            $show_state     = (int)($conf['show_state']     ?? 0);
            $logo_height    = (int)($conf['logo_height']    ?? 20);
            $logo_align     = htmlspecialchars($conf['logo_align']    ?? 'left', ENT_QUOTES);
            $date_format    = htmlspecialchars($conf['date_format']   ?? 'd.m.Y', ENT_QUOTES);

            $email_badge = $email_mode == 1
                ? '<span class="badge bg-success">ON</span>'
                : '<span class="badge border border-secondary text-secondary">OFF</span>';
            $star_icon = $is_default
                ? '<i class="ti ti-star-filled text-warning ms-1"></i>'
                : '';

            echo '<tr>';
            echo "<td>$i</td>";
            echo '<td>' . htmlspecialchars($conf['name'] ?? '') . $star_icon . '</td>';
            echo '<td>' . htmlspecialchars($conf['font'] ?? '') . '</td>';
            echo '<td>' . htmlspecialchars($conf['orientation'] ?? '') . '</td>';
            echo "<td>$email_badge</td>";
            $star_btn_class = $is_default ? 'btn-warning' : 'btn-outline-secondary';
            $star_btn_icon  = $is_default ? 'ti-star-filled' : 'ti-star';
            $toggle_url = $action . '?toggle_default=1&id=' . $id . '&_glpi_csrf_token=' . Session::getNewCSRFToken();
            echo '<td class="text-nowrap">';
            echo "<a href='" . htmlspecialchars($toggle_url) . "' class='btn btn-sm $star_btn_class me-1'"
                . " title='" . ($is_default ? __('Unset default') : __('Set as default')) . "'>"
                . "<i class='ti $star_btn_icon'></i></a>";
            echo "<button type='button' class='btn btn-sm btn-outline-secondary me-1 btn-edit-template'"
                . " data-id='$id'"
                . " data-name='$name'"
                . " data-font='$font'"
                . " data-fontsize='$fontsize'"
                . " data-header-color='$header_color'"
                . " data-breakword='$breakword'"
                . " data-city='$city'"
                . " data-orientation='$orientation'"
                . " data-serial-mode='$serial_mode'"
                . " data-man-mode='$man_mode'"
                . " data-upper-content='$upper_content'"
                . " data-content='$content'"
                . " data-footer='$footer'"
                . " data-email-mode='$email_mode'"
                . " data-email-template='$email_template'"
                . " data-logo='$logo'"
                . " data-show-state='$show_state'"
                . " data-logo-height='$logo_height'"
                . " data-logo-align='$logo_align'"
                . " data-date-format='$date_format'"
                . " data-bs-toggle='modal' data-bs-target='#modal-template'>"
                . "<i class='ti ti-edit'></i></button>";
            echo "<button type='button' class='btn btn-sm btn-outline-danger btn-delete'"
                . " data-id='$id'"
                . " data-name='$name'"
                . " data-type='template'"
                . " data-bs-toggle='modal' data-bs-target='#modal-delete'>"
                . "<i class='ti ti-trash'></i></button>";
            echo '</td></tr>';
            $i++;
        }

        echo '</tbody></table>';
    }

    static function showEmailConfigs() {
        global $DB;

        $rows = [];
        foreach ($DB->request(['FROM' => 'glpi_plugin_protocolsmanager_emailconfig']) as $conf) {
            $rows[] = $conf;
        }

        if (empty($rows)) {
            echo '<div class="p-3 text-muted">' . __('No item found') . '</div>';
            return;
        }

        echo '<table class="table table-hover mb-0"><thead><tr>';
        echo '<th style="width:3rem">#</th>';
        echo '<th>' . __('Name') . '</th>';
        echo '<th>' . __('Send to user') . '</th>';
        echo '<th style="width:9rem">' . __('Actions') . '</th>';
        echo '</tr></thead><tbody>';

        $i = 1;
        foreach ($rows as $conf) {
            $id            = (int) $conf['id'];
            $tname         = htmlspecialchars($conf['tname']         ?? '', ENT_QUOTES);
            $send_user     = (int)($conf['send_user']     ?? 2);
            $email_subject = htmlspecialchars($conf['email_subject'] ?? '', ENT_QUOTES);
            $email_content = htmlspecialchars($conf['email_content'] ?? '', ENT_QUOTES);
            $recipients    = htmlspecialchars($conf['recipients']    ?? '', ENT_QUOTES);

            $send_label = $send_user == 1 ? __('Yes') : __('No');

            echo '<tr>';
            echo "<td>$i</td>";
            echo '<td>' . htmlspecialchars($conf['tname'] ?? '') . '</td>';
            echo "<td>$send_label</td>";
            echo '<td>';
            echo "<button type='button' class='btn btn-sm btn-outline-secondary me-1 btn-edit-email'"
                . " data-id='$id'"
                . " data-tname='$tname'"
                . " data-send-user='$send_user'"
                . " data-email-subject='$email_subject'"
                . " data-email-content='$email_content'"
                . " data-recipients='$recipients'"
                . " data-bs-toggle='modal' data-bs-target='#modal-email'>"
                . "<i class='ti ti-edit'></i></button>";
            echo "<button type='button' class='btn btn-sm btn-outline-danger btn-delete'"
                . " data-id='$id'"
                . " data-name='$tname'"
                . " data-type='email'"
                . " data-bs-toggle='modal' data-bs-target='#modal-delete'>"
                . "<i class='ti ti-trash'></i></button>";
            echo '</td></tr>';
            $i++;
        }

        echo '</tbody></table>';
    }

    static function renderTemplateModal($action) {
        global $DB;

        $fonts = [
            'DejaVu Sans'      => 'DejaVu Sans',
            'DejaVu Serif'     => 'DejaVu Serif',
            'DejaVu Sans Mono' => 'DejaVu Sans Mono',
            'Roboto'           => 'Roboto',
            'Noto Serif'       => 'Noto Serif',
            'Helvetica'        => 'Helvetica',
        ];
        $fontsizes    = ['7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12'];
        $orientations = ['Portrait'=>__('Portrait'),'Landscape'=>__('Landscape')];

        echo '<div class="modal fade" id="modal-template" tabindex="-1" aria-hidden="true">';
        echo '<div class="modal-dialog modal-xl">';
        echo '<div class="modal-content">';

        echo '<div class="modal-header">';
        echo '<h5 class="modal-title" id="modal-template-title">' . __('Add template') . '</h5>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>';
        echo '</div>';

        echo '<form method="post" action="' . $action . '" enctype="multipart/form-data">';
        echo '<input type="hidden" name="MAX_FILE_SIZE" value="1948000">';
        echo '<input type="hidden" name="save" value="1">';
        echo '<input type="hidden" name="mode" id="tpl-mode" value="0">';
        echo '<input type="hidden" name="_glpi_csrf_token" value="' . Session::getNewCSRFToken() . '">';

        echo '<div class="modal-body">';

        // ── Section: Basic ─────────────────────────────────────────
        echo '<div class="d-flex align-items-center gap-2 mb-3">'
            . '<small class="text-uppercase fw-semibold text-secondary text-nowrap">' . __('Basic settings') . '</small>'
            . '<hr class="flex-grow-1 m-0"></div>';
        echo '<div class="row g-3 mb-4">';

        echo '<div class="col-12">';
        echo '<label class="form-label">' . __('Template name') . ' *</label>';
        echo '<input type="text" class="form-control" name="template_name" id="tpl-name" required>';
        echo '</div>';

        echo '<div class="col-md-5">';
        echo '<label class="form-label">Font</label>';
        echo '<select class="form-select" name="font" id="tpl-font">';
        foreach ($fonts as $val => $label) {
            echo '<option value="' . htmlspecialchars($val, ENT_QUOTES) . '">'
                . htmlspecialchars($label) . '</option>';
        }
        echo '</select></div>';

        echo '<div class="col-md-3">';
        echo '<label class="form-label">' . __('Font size') . '</label>';
        echo '<select class="form-select" name="fontsize" id="tpl-fontsize">';
        foreach ($fontsizes as $val => $label) {
            echo '<option value="' . $val . '">' . $label . '</option>';
        }
        echo '</select></div>';

        echo '<div class="col-md-4">';
        echo '<label class="form-label">' . __('Table header color') . '</label>';
        echo '<input type="color" class="form-control form-control-color w-100" name="header_color" id="tpl-header-color" value="#dee2e6">';
        echo '</div>';

        echo '<div class="col-md-4">';
        echo '<label class="form-label d-block">' . __('Orientation') . '</label>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="orientation" id="orientation-portrait" value="Portrait" checked>';
        echo '<label class="form-check-label" for="orientation-portrait">' . __('Portrait') . '</label></div>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="orientation" id="orientation-landscape" value="Landscape">';
        echo '<label class="form-check-label" for="orientation-landscape">' . __('Landscape') . '</label></div>';
        echo '</div>';

        echo '<div class="col-md-4">';
        $breakword_tip = htmlspecialchars('ON: long words wrap inside cells, column widths are fixed. OFF: column widths adjust to content dynamically.');
        echo '<label class="form-label d-block">' . __('Word breaking')
            . ' <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="' . $breakword_tip . '"></i></label>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="breakword" id="breakword-on" value="1">';
        echo '<label class="form-check-label" for="breakword-on">On</label></div>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="breakword" id="breakword-off" value="0" checked>';
        echo '<label class="form-check-label" for="breakword-off">Off</label></div>';
        echo '</div>';

        echo '<div class="col-md-4">';
        $serial_tip = htmlspecialchars('Separate columns: serial number and inventory number in separate columns. One column: shows serial number, or inventory number if serial is missing.');
        echo '<label class="form-label d-block">' . __('Serial number') . ' / ' . __('Inventory number')
            . ' <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="' . $serial_tip . '"></i></label>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="serial_mode" id="serial-mode-1" value="1" checked>';
        echo '<label class="form-check-label" for="serial-mode-1">' . __('Separate columns') . '</label></div>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="serial_mode" id="serial-mode-2" value="2">';
        echo '<label class="form-check-label" for="serial-mode-2">' . __('One column') . '</label></div>';
        echo '</div>';

        echo '<div class="col-md-4">';
        $man_tip = htmlspecialchars('One column: manufacturer and model shown together (e.g. "Dell OptiPlex 7090"). Separate columns: manufacturer and model in separate columns.');
        echo '<label class="form-label d-block">' . __('Manufacturer') . ' / ' . __('Model')
            . ' <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="' . $man_tip . '"></i></label>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="man_mode" id="man-mode-1" value="1" checked>';
        echo '<label class="form-check-label" for="man-mode-1">' . __('One column') . '</label></div>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="man_mode" id="man-mode-2" value="2">';
        echo '<label class="form-check-label" for="man-mode-2">' . __('Separate columns') . '</label></div>';
        echo '</div>';

        echo '<div class="col-md-4">';
        $state_tip = htmlspecialchars('Adds a Status column to the PDF table showing the GLPI status/state of each asset.');
        echo '<label class="form-label d-block">' . __('Status column')
            . ' <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="' . $state_tip . '"></i></label>';
        echo '<div class="form-check">';
        echo '<input class="form-check-input" type="checkbox" name="show_state" id="tpl-show-state" value="1">';
        echo '<label class="form-check-label" for="tpl-show-state">' . __('Show in PDF') . '</label>';
        echo '</div>';
        echo '</div>';

        echo '<div class="col-md-6">';
        $logo_tip = htmlspecialchars('PNG or JPEG, max ~2 MB. Height is set in mm; width is auto-calculated to preserve aspect ratio.');
        echo '<label class="form-label">' . __('Logo')
            . ' <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="' . $logo_tip . '"></i></label>';
        echo '<input type="file" class="form-control" name="logo" id="tpl-logo" accept="image/png,image/jpeg">';
        echo '<div class="mt-1 small text-muted" id="tpl-logo-info"></div>';
        echo '<div class="form-check mt-1" id="tpl-logo-delete-wrap" style="display:none">';
        echo '<input class="form-check-input" type="checkbox" name="img_delete" id="tpl-img-delete" value="1">';
        echo '<label class="form-check-label text-danger" for="tpl-img-delete">' . __('Delete') . ' ' . __('File') . '</label>';
        echo '</div></div>';

        echo '<div class="col-md-2">';
        echo '<label class="form-label">' . __('Height') . ' (mm)</label>';
        echo '<input type="number" class="form-control" name="logo_height" id="tpl-logo-height" value="20" min="5" max="100">';
        echo '</div>';

        echo '<div class="col-md-4">';
        echo '<label class="form-label d-block">' . __('Logo alignment') . '</label>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="logo_align" id="logo-align-left" value="left" checked>';
        echo '<label class="form-check-label" for="logo-align-left">' . __('Left') . '</label></div>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="logo_align" id="logo-align-center" value="center">';
        echo '<label class="form-check-label" for="logo-align-center">' . __('Center') . '</label></div>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="logo_align" id="logo-align-right" value="right">';
        echo '<label class="form-check-label" for="logo-align-right">' . __('Right') . '</label></div>';
        echo '</div>';

        echo '</div>'; // row basic

        // ── Section: Content ───────────────────────────────────────
        echo '<div class="d-flex align-items-center gap-2 mb-3">'
            . '<small class="text-uppercase fw-semibold text-secondary text-nowrap">' . __('Content') . '</small>'
            . '<hr class="flex-grow-1 m-0"></div>';
        echo '<div class="row g-3 mb-4">';

        echo '<div class="col-md-6">';
        echo '<label class="form-label">' . __('City') . '</label>';
        echo '<input type="text" class="form-control" name="city" id="tpl-city">';
        echo '</div>';

        echo '<div class="col-md-6">';
        $date_formats = ['d.m.Y', 'd/m/Y', 'm/d/Y', 'Y-m-d'];
        $date_tip = htmlspecialchars('Used for the header date next to the city and for the {cur_date} placeholder.');
        echo '<label class="form-label">' . __('Date format')
            . ' <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="' . $date_tip . '"></i></label>';
        echo '<select class="form-select" name="date_format" id="tpl-date-format">';
        foreach ($date_formats as $fmt) {
            echo '<option value="' . htmlspecialchars($fmt, ENT_QUOTES) . '">'
                . htmlspecialchars(date($fmt) . ' (' . $fmt . ')') . '</option>';
        }
        echo '</select></div>';

        $ph_tip = htmlspecialchars('{cur_date} — current date, {owner} — asset owner name, {admin} — logged-in user name');

        echo '<div class="col-12">';
        echo '<label class="form-label">' . __('Upper Content')
            . ' <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="' . $ph_tip . '"></i></label>';
        echo '<textarea class="form-control" name="template_uppercontent" id="tpl-upper-content" rows="3"></textarea>';
        echo '</div>';

        echo '<div class="col-12">';
        echo '<label class="form-label">' . __('Content')
            . ' <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="' . $ph_tip . '"></i></label>';
        echo '<textarea class="form-control" name="template_content" id="tpl-content" rows="3"></textarea>';
        echo '</div>';

        echo '<div class="col-12">';
        echo '<label class="form-label">' . __('Footer')
            . ' <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="' . $ph_tip . '"></i></label>';
        echo '<textarea class="form-control" name="footer_text" id="tpl-footer" rows="2"></textarea>';
        echo '</div>';

        echo '</div>'; // row content

        // ── Section: Email ─────────────────────────────────────────
        echo '<div class="d-flex align-items-center gap-2 mb-3">'
            . '<small class="text-uppercase fw-semibold text-secondary text-nowrap">' . __('Email') . '</small>'
            . '<hr class="flex-grow-1 m-0"></div>';
        echo '<div class="row g-3">';

        echo '<div class="col-md-6">';
        $email_tip = htmlspecialchars('When ON, an email is automatically sent after generating the protocol, using the selected email template below.');
        echo '<label class="form-label d-block">' . __('Enable email autosending')
            . ' <i class="ti ti-info-circle text-muted" data-bs-toggle="tooltip" title="' . $email_tip . '"></i></label>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="email_mode" id="email-mode-on" value="1">';
        echo '<label class="form-check-label" for="email-mode-on">ON</label></div>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="email_mode" id="email-mode-off" value="2" checked>';
        echo '<label class="form-check-label" for="email-mode-off">OFF</label></div>';
        echo '</div>';

        echo '<div class="col-md-6">';
        echo '<label class="form-label">' . __('Email template') . '</label>';
        echo '<select class="form-select" name="email_template" id="tpl-email-template">';
        echo '<option value="0">—</option>';
        foreach ($DB->request(['FROM' => 'glpi_plugin_protocolsmanager_emailconfig']) as $etpl) {
            echo '<option value="' . (int)$etpl['id'] . '">'
                . htmlspecialchars($etpl['tname'] ?? '') . '</option>';
        }
        echo '</select>';
        echo '<div class="invalid-feedback" id="email-template-feedback">'
            . __('Select an email template or disable autosending.') . '</div>';
        echo '</div>';

        echo '</div>'; // row logo & email

        echo '</div>'; // modal-body

        echo '<div class="modal-footer">';
        echo '<button type="button" class="btn btn-outline-secondary me-auto" id="btn-preview-template">'
            . '<i class="ti ti-eye me-1"></i>' . __('Preview') . '</button>';
        echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . __('Cancel') . '</button>';
        echo '<button type="submit" class="btn btn-primary">' . __('Save') . '</button>';
        echo '</div>';

        echo '</form>';
        echo '</div></div></div>'; // modal-content, dialog, modal
    }

    static function renderEmailModal($action) {
        echo '<div class="modal fade" id="modal-email" tabindex="-1" aria-hidden="true">';
        echo '<div class="modal-dialog modal-lg">';
        echo '<div class="modal-content">';

        echo '<div class="modal-header">';
        echo '<h5 class="modal-title" id="modal-email-title">' . __('Add email template') . '</h5>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>';
        echo '</div>';

        echo '<form method="post" action="' . $action . '">';
        echo '<input type="hidden" name="save_email" value="1">';
        echo '<input type="hidden" name="email_edit_id" id="email-id" value="0">';
        echo '<input type="hidden" name="_glpi_csrf_token" value="' . Session::getNewCSRFToken() . '">';

        echo '<div class="modal-body">';
        echo '<div class="row g-3">';

        echo '<div class="col-12">';
        echo '<label class="form-label">' . __('Template name') . ' *</label>';
        echo '<input type="text" class="form-control" name="tname" id="email-tname" required>';
        echo '</div>';

        echo '<div class="col-12">';
        echo '<label class="form-label d-block">' . __('Send to user') . '</label>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="send_user" id="send-user-yes" value="1">';
        echo '<label class="form-check-label" for="send-user-yes">' . __('send to user') . '</label></div>';
        echo '<div class="form-check form-check-inline">';
        echo '<input class="form-check-input" type="radio" name="send_user" id="send-user-no" value="2" checked>';
        echo '<label class="form-check-label" for="send-user-no">' . __("don't send to user") . '</label></div>';
        echo '</div>';

        echo '<div class="col-12">';
        echo '<label class="form-label">' . __('Email subject') . ' *</label>';
        echo '<input type="text" class="form-control" name="email_subject" id="email-subject" required>';
        echo '</div>';

        echo '<div class="col-12">';
        echo '<label class="form-label">' . __('Email content') . ' *</label>';
        echo '<textarea class="form-control" name="email_content" id="email-content" rows="5" required></textarea>';
        echo '</div>';

        echo '<div class="col-12">';
        echo '<label class="form-label">' . __('Add emails - use ; to separate') . ' *</label>';
        echo '<textarea class="form-control" name="recipients" id="email-recipients" rows="3" required></textarea>';
        echo '</div>';

        echo '</div></div>'; // row, modal-body

        echo '<div class="modal-footer">';
        echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . __('Cancel') . '</button>';
        echo '<button type="submit" class="btn btn-primary">' . __('Save') . '</button>';
        echo '</div>';

        echo '</form>';
        echo '</div></div></div>';
    }

    static function renderDeleteModal($action) {
        echo '<div class="modal fade" id="modal-delete" tabindex="-1" aria-hidden="true">';
        echo '<div class="modal-dialog modal-sm">';
        echo '<div class="modal-content">';

        echo '<div class="modal-header">';
        echo '<h5 class="modal-title">' . __('Delete') . '</h5>';
        echo '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>';
        echo '</div>';

        echo '<div class="modal-body">';
        echo '<p id="modal-delete-msg"></p>';
        echo '</div>';

        echo '<div class="modal-footer">';
        echo '<form method="post" action="' . $action . '" id="form-delete">';
        echo '<input type="hidden" name="_glpi_csrf_token" value="' . Session::getNewCSRFToken() . '">';
        echo '<input type="hidden" name="conf_id"       id="delete-conf-id"   value="">';
        echo '<input type="hidden" name="email_conf_id" id="delete-email-id"  value="">';
        echo '<input type="hidden" name="delete"        id="delete-tpl"       value="">';
        echo '<input type="hidden" name="delete_email"  id="delete-eml"       value="">';
        echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . __('Cancel') . '</button>';
        echo '<button type="submit" class="btn btn-danger ms-2">' . __('Delete') . '</button>';
        echo '</form>';
        echo '</div>';

        echo '</div></div></div>';
    }

    static function renderScript($preview_url = '', $token_url = '') {
        $title_add_tpl  = json_encode(__('Add template'));
        $title_edit_tpl = json_encode(__('Edit template'));
        $title_add_eml  = json_encode(__('Add email template'));
        $title_edit_eml = json_encode(__('Edit email template'));
        $label_current  = json_encode(__('Current') . ': ');
        $label_delete_q = json_encode(__('Delete') . ' "');
        $js_preview_url = json_encode($preview_url);
        $js_token_url   = json_encode($token_url);
        $label_popup_blocked = json_encode(__('Please allow popups for this site.'));

        echo <<<JS
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tab persistence ──────────────────────────────────────────
    var params = new URLSearchParams(window.location.search);
    if (params.get('tab') === 'email' || window.location.hash === '#email') {
        var emailBtn = document.getElementById('tab-email-btn');
        if (emailBtn) bootstrap.Tab.getOrCreateInstance(emailBtn).show();
    }
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (btn) {
        btn.addEventListener('shown.bs.tab', function (e) {
            var target = e.target.dataset.bsTarget;
            history.replaceState(null, '', target === '#tab-email' ? '#email' : window.location.pathname);
        });
    });

    // ── Template modal: Add ───────────────────────────────────────
    var btnAddTpl = document.getElementById('btn-add-template');
    if (btnAddTpl) {
        btnAddTpl.addEventListener('click', function () {
            document.getElementById('modal-template-title').textContent = $title_add_tpl;
            document.getElementById('tpl-mode').value        = '0';
            document.getElementById('tpl-name').value        = '';
            document.getElementById('tpl-font').value        = 'DejaVu Sans';
            document.getElementById('tpl-fontsize').value    = '9';
            document.getElementById('tpl-header-color').value = '#dee2e6';
            var bw0 = document.getElementById('breakword-off'); if (bw0) bw0.checked = true;
            document.getElementById('tpl-city').value        = '';
            var op = document.getElementById('orientation-portrait'); if (op) op.checked = true;
            var sm1 = document.getElementById('serial-mode-1'); if (sm1) sm1.checked = true;
            var mm1 = document.getElementById('man-mode-1'); if (mm1) mm1.checked = true;
            document.getElementById('tpl-upper-content').value = '';
            document.getElementById('tpl-content').value     = '';
            document.getElementById('tpl-footer').value      = '';
            var em2 = document.getElementById('email-mode-off'); if (em2) em2.checked = true;
            var etpl = document.getElementById('tpl-email-template');
            if (etpl) etpl.value = '0';
            var li = document.getElementById('tpl-logo-info');
            li.textContent = ''; li.dataset.logo = '';
            document.getElementById('tpl-logo-delete-wrap').style.display = 'none';
            document.getElementById('tpl-img-delete').checked = false;
            document.getElementById('tpl-show-state').checked = false;
            document.getElementById('tpl-logo-height').value = '20';
            var laLeft = document.getElementById('logo-align-left'); if (laLeft) laLeft.checked = true;
            document.getElementById('tpl-date-format').value = 'd.m.Y';
        });
    }

    // ── Template modal: Edit ──────────────────────────────────────
    document.querySelectorAll('.btn-edit-template').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var d = this.dataset;
            document.getElementById('modal-template-title').textContent = $title_edit_tpl;
            document.getElementById('tpl-mode').value        = d.id;
            document.getElementById('tpl-name').value        = d.name;
            document.getElementById('tpl-font').value        = d.font;
            document.getElementById('tpl-fontsize').value    = d.fontsize;
            document.getElementById('tpl-header-color').value = d.headerColor;
            var bw = document.querySelector('input[name="breakword"][value="' + d.breakword + '"]');
            if (bw) bw.checked = true;
            document.getElementById('tpl-city').value        = d.city;
            var ori = document.querySelector('input[name="orientation"][value="' + d.orientation + '"]');
            if (ori) ori.checked = true;
            var sm = document.querySelector('input[name="serial_mode"][value="' + d.serialMode + '"]');
            if (sm) sm.checked = true;
            var mm = document.querySelector('input[name="man_mode"][value="' + d.manMode + '"]');
            if (mm) mm.checked = true;
            document.getElementById('tpl-upper-content').value = d.upperContent;
            document.getElementById('tpl-content').value     = d.content;
            document.getElementById('tpl-footer').value      = d.footer;
            var em = document.querySelector('input[name="email_mode"][value="' + d.emailMode + '"]');
            if (em) em.checked = true;
            var etpl = document.getElementById('tpl-email-template');
            if (etpl) etpl.value = d.emailTemplate || '0';
            var logoInfo = document.getElementById('tpl-logo-info');
            var logoWrap = document.getElementById('tpl-logo-delete-wrap');
            if (d.logo) {
                logoInfo.textContent = $label_current + d.logo;
                logoInfo.dataset.logo = d.logo;
                logoWrap.style.display = 'block';
            } else {
                logoInfo.textContent = '';
                logoInfo.dataset.logo = '';
                logoWrap.style.display = 'none';
            }
            document.getElementById('tpl-img-delete').checked = false;
            document.getElementById('tpl-show-state').checked = d.showState === '1';
            document.getElementById('tpl-logo-height').value = d.logoHeight || '20';
            var laEl = document.querySelector('input[name="logo_align"][value="' + (d.logoAlign || 'left') + '"]');
            if (laEl) laEl.checked = true;
            document.getElementById('tpl-date-format').value = d.dateFormat || 'd.m.Y';
        });
    });

    // ── Email modal: Add ──────────────────────────────────────────
    var btnAddEml = document.getElementById('btn-add-email');
    if (btnAddEml) {
        btnAddEml.addEventListener('click', function () {
            document.getElementById('modal-email-title').textContent = $title_add_eml;
            document.getElementById('email-id').value       = '0';
            document.getElementById('email-tname').value    = '';
            var sn = document.getElementById('send-user-no'); if (sn) sn.checked = true;
            document.getElementById('email-subject').value  = '';
            document.getElementById('email-content').value  = '';
            document.getElementById('email-recipients').value = '';
        });
    }

    // ── Email modal: Edit ─────────────────────────────────────────
    document.querySelectorAll('.btn-edit-email').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var d = this.dataset;
            document.getElementById('modal-email-title').textContent = $title_edit_eml;
            document.getElementById('email-id').value          = d.id;
            document.getElementById('email-tname').value       = d.tname;
            var su = document.querySelector('input[name="send_user"][value="' + d.sendUser + '"]');
            if (su) su.checked = true;
            document.getElementById('email-subject').value     = d.emailSubject;
            document.getElementById('email-content').value     = d.emailContent;
            document.getElementById('email-recipients').value  = d.recipients;
        });
    });

    // ── Template modal: email template validation ─────────────────
    var modalTplForm = document.querySelector('#modal-template form');
    if (modalTplForm) {
        modalTplForm.addEventListener('submit', function (e) {
            var em   = document.querySelector('input[name="email_mode"]:checked');
            var etpl = document.getElementById('tpl-email-template');
            if (em && em.value === '1' && etpl && (etpl.value === '0' || etpl.value === '')) {
                e.preventDefault();
                etpl.classList.add('is-invalid');
                var fb = document.getElementById('email-template-feedback');
                if (fb) fb.style.display = 'block';
                etpl.focus();
            }
        });
        document.querySelectorAll('input[name="email_mode"]').forEach(function (r) {
            r.addEventListener('change', function () {
                var etpl = document.getElementById('tpl-email-template');
                if (etpl) { etpl.classList.remove('is-invalid'); }
            });
        });
        document.getElementById('tpl-email-template').addEventListener('change', function () {
            this.classList.remove('is-invalid');
        });
    }

    // ── Template preview (fetch → blob → new tab) ────────────────
    var _previewUrl = $js_preview_url;
    var _tokenUrl   = $js_token_url;
    var btnPreview  = document.getElementById('btn-preview-template');
    if (btnPreview) {
        btnPreview.addEventListener('click', function () {
            // Open blank window NOW (synchronous user action = no popup blocker)
            var win = window.open('about:blank', '_blank');
            if (!win) { alert($label_popup_blocked); return; }

            // Collect template data
            var bw  = document.querySelector('input[name="breakword"]:checked');
            var ori = document.querySelector('input[name="orientation"]:checked');
            var sm  = document.querySelector('input[name="serial_mode"]:checked');
            var mm  = document.querySelector('input[name="man_mode"]:checked');
            var logoInfo = document.getElementById('tpl-logo-info');

            var payload = {
                template_name:         document.getElementById('tpl-name').value,
                font:                  document.getElementById('tpl-font').value,
                fontsize:              document.getElementById('tpl-fontsize').value,
                header_color:          document.getElementById('tpl-header-color').value,
                breakword:             bw  ? bw.value  : '0',
                city:                  document.getElementById('tpl-city').value,
                orientation:           ori ? ori.value : 'Portrait',
                serial_mode:           sm  ? sm.value  : '1',
                man_mode:              mm  ? mm.value  : '1',
                template_uppercontent: document.getElementById('tpl-upper-content').value,
                template_content:      document.getElementById('tpl-content').value,
                footer_text:           document.getElementById('tpl-footer').value,
                logo_existing:         logoInfo ? (logoInfo.dataset.logo || '') : '',
                show_state:            document.getElementById('tpl-show-state').checked ? '1' : '0',
                logo_height:           document.getElementById('tpl-logo-height').value,
                logo_align:            (function(){ var r = document.querySelector('input[name="logo_align"]:checked'); return r ? r.value : 'left'; })(),
                date_format:           document.getElementById('tpl-date-format').value
            };

            // Get a fresh CSRF token, then POST to preview
            fetch(_tokenUrl)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    payload._glpi_csrf_token = data.token;
                    var fd = new FormData();
                    Object.keys(payload).forEach(function (k) { fd.append(k, payload[k]); });
                    return fetch(_previewUrl, { method: 'POST', body: fd });
                })
                .then(function (r) {
                    if (!r.ok) { win.close(); return Promise.reject('HTTP ' + r.status); }
                    return r.arrayBuffer();
                })
                .then(function (buf) {
                    var blob = new Blob([buf], { type: 'application/pdf' });
                    win.location.href = URL.createObjectURL(blob);
                })
                .catch(function () { win.close(); });
        });
    }

    // ── Delete modal ──────────────────────────────────────────────
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var d = this.dataset;
            document.getElementById('modal-delete-msg').textContent = $label_delete_q + d.name + '"?';
            if (d.type === 'template') {
                document.getElementById('delete-conf-id').value  = d.id;
                document.getElementById('delete-email-id').value = '';
                document.getElementById('delete-tpl').value      = '1';
                document.getElementById('delete-eml').value      = '';
            } else {
                document.getElementById('delete-conf-id').value  = '';
                document.getElementById('delete-email-id').value = d.id;
                document.getElementById('delete-tpl').value      = '';
                document.getElementById('delete-eml').value      = '1';
            }
        });
    });

});
</script>
JS;
    }

    static function saveConfigs() {
        global $DB;

        if (empty($_POST["template_name"])) {
            Session::addMessageAfterRedirect('Fill mandatory fields', 'WARNING', true);
            return;
        }

        if (!empty($_POST["email_mode"]) && (int)$_POST["email_mode"] === 1
                && empty($_POST["email_template"])) {
            Session::addMessageAfterRedirect('Email autosending is ON but no email template is selected.', 'WARNING', true);
            return;
        }

        $template_name         = $_POST['template_name'];
        $template_uppercontent = $_POST['template_uppercontent'];
        $template_content      = $_POST['template_content'];
        $template_footer       = $_POST['footer_text'];
        $font                  = $_POST["font"];
        $fontsize              = $_POST["fontsize"];
        $city                  = $_POST["city"];
        $mode                  = (int) $_POST["mode"];
        $serial_mode           = $_POST["serial_mode"];
        $man_mode              = (int)($_POST["man_mode"] ?? 1);
        $show_state            = !empty($_POST["show_state"]) ? 1 : 0;
        $logo_height           = !empty($_POST["logo_height"]) ? (int)$_POST["logo_height"] : 20;
        $logo_align            = in_array($_POST["logo_align"] ?? '', ['left','center','right']) ? $_POST["logo_align"] : 'left';
        $date_format           = in_array($_POST["date_format"] ?? '', ['d.m.Y','d/m/Y','m/d/Y','Y-m-d']) ? $_POST["date_format"] : 'd.m.Y';
        $orientation           = $_POST["orientation"];
        $breakword             = $_POST["breakword"];
        $email_mode            = $_POST["email_mode"];
        $email_template        = $_POST["email_template"];
        $header_color          = $_POST["header_color"];
        $full_img_name = null;
        if (!empty($_FILES['logo']['name'])) {
            $full_img_name = self::uploadImage();
        }

        $data = [
            'name'           => $template_name,
            'upper_content'  => $template_uppercontent,
            'content'        => $template_content,
            'footer'         => $template_footer,
            'font'           => $font,
            'fontsize'       => $fontsize,
            'city'           => $city,
            'serial_mode'    => $serial_mode,
            'man_mode'       => $man_mode,
            'show_state'     => $show_state,
            'logo_height'    => $logo_height,
            'logo_align'     => $logo_align,
            'date_format'    => $date_format,
            'orientation'    => $orientation,
            'breakword'      => $breakword,
            'email_mode'     => $email_mode,
            'email_template' => $email_template,
            'header_color'   => $header_color,
        ];

        if ($mode === 0) {
            $data['logo'] = $full_img_name;
            $DB->insert('glpi_plugin_protocolsmanager_configs', $data);
        } else {
            if ($full_img_name !== null) {
                $data['logo'] = $full_img_name;
            } elseif (!empty($_POST['img_delete'])) {
                $data['logo'] = null;
            }
            $DB->update('glpi_plugin_protocolsmanager_configs', $data, ['id' => $mode]);
        }

        Session::addMessageAfterRedirect('Config saved');
    }

    static function saveEmailConfigs() {
        global $DB;

        if (empty($_POST["email_subject"]) || empty($_POST["email_content"])
                || empty($_POST["recipients"]) || empty($_POST["tname"])) {
            Session::addMessageAfterRedirect('Fill mandatory fields', 'WARNING', true);
            return;
        }

        $tname         = $_POST["tname"];
        $send_user     = $_POST["send_user"];
        $email_subject = $_POST["email_subject"];
        $email_content = $_POST["email_content"];
        $recipients    = $_POST["recipients"];
        $email_edit_id = (int) $_POST["email_edit_id"];

        $data = [
            'tname'         => $tname,
            'send_user'     => $send_user,
            'email_subject' => $email_subject,
            'email_content' => $email_content,
            'recipients'    => $recipients,
        ];

        if ($email_edit_id === 0) {
            $DB->insert('glpi_plugin_protocolsmanager_emailconfig', $data);
        } else {
            $DB->update('glpi_plugin_protocolsmanager_emailconfig', $data, ['id' => $email_edit_id]);
        }

        Session::addMessageAfterRedirect('Config saved');
    }

    static function uploadImage() {
        if (!empty($_FILES['logo']['name'])) {
            if ($_FILES['logo']['error'] !== UPLOAD_ERR_FORM_SIZE) {
                if (!$_FILES['logo']['error']) {
                    $type = $_FILES['logo']['type'];
                    if (in_array($type, ['image/jpeg', 'image/jpg', 'image/png'])) {
                        $ext           = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                        $full_img_name = 'logo' . time() . '.' . $ext;
                        move_uploaded_file($_FILES['logo']['tmp_name'], GLPI_ROOT . '/files/_pictures/' . $full_img_name);
                        return $full_img_name;
                    } else {
                        Session::addMessageAfterRedirect('Wrong file type. Only .jpg and .png files accepted', 'WARNING', true);
                    }
                } else {
                    Session::addMessageAfterRedirect(__('Unknown error'), 'WARNING', true);
                }
            } else {
                Session::addMessageAfterRedirect('File size too large', 'WARNING', true);
            }
        }
        return null;
    }

    static function toggleDefault(int $id) {
        global $DB;
        $current = 0;
        foreach ($DB->request(['FROM' => 'glpi_plugin_protocolsmanager_configs', 'WHERE' => ['id' => $id], 'FIELDS' => ['is_default']]) as $row) {
            $current = (int)$row['is_default'];
        }
        if ($current) {
            $DB->update('glpi_plugin_protocolsmanager_configs', ['is_default' => 0], ['id' => $id]);
        } else {
            $DB->update('glpi_plugin_protocolsmanager_configs', ['is_default' => 0], [true]);
            $DB->update('glpi_plugin_protocolsmanager_configs', ['is_default' => 1], ['id' => $id]);
        }
    }

    static function deleteConfigs() {
        global $DB;
        $conf_id = (int) $_POST['conf_id'];
        $DB->delete('glpi_plugin_protocolsmanager_configs', ['id' => $conf_id]);
    }

    static function deleteEmailConfigs() {
        global $DB;
        $email_conf_id = (int) $_POST['email_conf_id'];
        $DB->delete('glpi_plugin_protocolsmanager_emailconfig', ['id' => $email_conf_id]);
    }
}
