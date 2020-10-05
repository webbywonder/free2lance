<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function pdf_create($html, $filename, $stream, $attachment = false)
{
    /* Fix issue with html css parser in DOMPDF */
    $html = preg_replace('/>\s+</', '><', $html);

    require_once 'vendor/dompdf/dompdf/dompdf_config.inc.php';
    global $_dompdf_show_warnings, $_dompdf_debug, $_DOMPDF_DEBUG_TYPES;


    $options = [];


    if (isset($_GET['input_file'])) {
        $file = rawurldecode($_GET['input_file']);
    } elseif //throw new DOMPDF_Exception("An input file is required (i.e. input_file _GET variable).");

    (isset($_GET['paper'])) {
        $paper = rawurldecode($_GET['paper']);
    } else {
        $paper = DOMPDF_DEFAULT_PAPER_SIZE;
    }

    if (isset($_GET['orientation'])) {
        $orientation = rawurldecode($_GET['orientation']);
    } else {
        $orientation = 'portrait';
    }

    if (isset($_GET['base_path'])) {
        $base_path = rawurldecode($_GET['base_path']);
        $file = $base_path . $file; // Set the input file
    }

    if (isset($_GET['options'])) {
        $options = $_GET['options'];
    }

    if ($attachment == 'show') {
        $options['Attachment'] = false;
    }

    $outfile = $filename . '.pdf'; // Don't allow them to set the output file
    $save_file = false; // Don't save the file


    $dompdf = new Dompdf\Dompdf();

    $options['isRemoteEnabled'] = true;
    $dompdf->set_option('isRemoteEnabled', true);


    /* Uncomment the line below in order to activate special characters (Chiniese, Korean,...)*/
    /* $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'); */
    $dompdf->load_html($html);



    if (isset($base_path)) {
        $dompdf->set_base_path($base_path);
    }

    $dompdf->set_paper($paper, $orientation);

    $dompdf->render();

    if ($_dompdf_show_warnings) {
        global $_dompdf_warnings;
        foreach ($_dompdf_warnings as $msg) {
            echo $msg . "\n";
        }
        echo $dompdf->get_canvas()->get_cpdf()->messages;
        flush();
    }

    if ($save_file) {
        //   if ( !is_writable($outfile) )
        //     throw new DOMPDF_Exception("'$outfile' is not writable.");
        ini_set('error_reporting', E_ERROR);
        if (!write_file('./files/temp/' . $filename . '.pdf', $pdf)) {
            echo 'files/temp/' . $filename . '.pdf' . ' -> PDF could not be saved! Check your server settings!';
            die();
        }
    }

    if (!headers_sent()) {
        if ($stream) {
            $dompdf->stream($outfile, $options);
            exit();
        } else {
            $pdf = $dompdf->output();

            ini_set('error_reporting', E_ERROR);
            if (!write_file('./files/temp/' . $filename . '.pdf', $pdf)) {
                echo 'files/temp/' . $filename . '.pdf' . ' -> PDF could not be saved! Check your server settings!';
                die();
            }
        }
    }
}
