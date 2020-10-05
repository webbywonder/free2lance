<?php

interface YandexAPI
{
    public function APIcall();
}

class Translator extends MY_Controller implements YandexAPI
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->helper('file');
        $this->load->helper('curl');

        function readNewLines($langType = 'application')
        {
            $langFile = fopen('./application/language/english/' . $langType . '_lang.php', 'r') or die('Unable to open file!');
            //echo "<pre>".htmlspecialchars(fread($langFile,filesize("./application/language/english/application_lang.php")));
            $i = 0;
            $newLinesArray = [];
            while (!feof($langFile)) {
                $a = fgets($langFile);

                if (strpos($a, '#new') !== false) {
                    $i++;
                    $newLinesArray[] = $a;
                }
            }
            fclose($langFile);

            return $newLinesArray;
        }

        function replaceLines($language = false, $langShort = false, $langtype = 'application')
        {
            $messages = '';
            $newLinesArray = readNewLines($langtype);
            $translation = TranslateAPI::APIcall($newLinesArray, $langShort);
            $ni = 0;
            $newLines = '';
            foreach ($newLinesArray as $value) {
                $return_value = preg_replace("/\s'.*?'/", " '" . addslashes($translation->text[$ni]) . "'", $value);
                $newLines .= str_replace('#new', '', $return_value);
                $ni++;
            }

            $target_file = './application/language/' . $language . '/' . $langtype . '_lang.php';
            $data = read_file($target_file);
            $data .= "\r\n\r\n" . $newLines;
            if (!write_file($target_file, $data)) {
                $messages .= 'Unable to write the file';
            } else {
                $messages .= $ni++ . ' New language lines written to ' . $target_file . '!';
                $messages .= '<pre>' . htmlspecialchars($newLines) . '</pre>';
            }
            return $messages;
        }

        if ($handle = opendir('./application/language/')) {
            $langFolders = [];
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..' && $entry != 'english') {
                    $langFolders[] = $entry;
                }
            }
            closedir($handle);
        }

        function langShortener($langFolder)
        {
            $langFolder = ucfirst($langFolder);
            switch ($langFolder) {
                case 'Albanian': $langShort = 'sq'; break; case 'English': $langShort = 'en'; break; case 'Arabic': $langShort = 'ar'; break; case 'Armenian': $langShort = 'hy'; break; case 'Azerbaijan': $langShort = 'az'; break; case 'Afrikaans': $langShort = 'af'; break; case 'Basque': $langShort = 'eu'; break; case 'Belarusian': $langShort = 'be'; break; case 'Bulgarian': $langShort = 'bg'; break; case 'Bosnian': $langShort = 'bs'; break; case 'Welsh': $langShort = 'cy'; break; case 'Vietnamese': $langShort = 'vi'; break; case 'Hungarian': $langShort = 'hu'; break; case 'Haitian (Creole)': $langShort = 'ht'; break; case 'Galician': $langShort = 'gl'; break; case 'Dutch': $langShort = 'nl'; break; case 'Greek': $langShort = 'el'; break; case 'Georgian': $langShort = 'ka'; break; case 'Danish': $langShort = 'da'; break; case 'Yiddish': $langShort = 'he'; break; case 'Indonesian': $langShort = 'id'; break; case 'Irish': $langShort = 'ga'; break; case 'Italian': $langShort = 'it'; break; case 'Icelandic': $langShort = 'is'; break; case 'Spanish': $langShort = 'es'; break; case 'Kazakh': $langShort = 'kk'; break; case 'Catalan': $langShort = 'ca'; break; case 'Kyrgyz': $langShort = 'ky'; break; case 'Chinese': $langShort = 'zh'; break; case 'Korean': $langShort = 'ko'; break; case 'Latin': $langShort = 'la'; break; case 'Latvian': $langShort = 'lv'; break; case 'Lithuanian': $langShort = 'lt'; break; case 'Malagasy': $langShort = 'mg'; break; case 'Malay': $langShort = 'ms'; break; case 'Maltese': $langShort = 'mt'; break; case 'Macedonian': $langShort = 'mk'; break; case 'Mongolian': $langShort = 'mn'; break; case 'German': $langShort = 'de'; break; case 'Norwegian': $langShort = 'no'; break; case 'Persian': $langShort = 'fa'; break; case 'Polish': $langShort = 'pl'; break; case 'Portuguese': $langShort = 'pt'; break; case 'Romanian': $langShort = 'ro'; break; case 'Russian': $langShort = 'ru'; break; case 'Serbian': $langShort = 'sr'; break; case 'Slovakian': $langShort = 'sk'; break; case 'Slovenian': $langShort = 'sl'; break; case 'Swahili': $langShort = 'sw'; break; case 'Tajik': $langShort = 'tg'; break; case 'Thai': $langShort = 'th'; break; case 'Tagalog': $langShort = 'tl'; break; case 'Tatar': $langShort = 'tt'; break; case 'Turkish': $langShort = 'tr'; break; case 'Uzbek': $langShort = 'uz'; break; case 'Ukrainian': $langShort = 'uk'; break; case 'Finish': $langShort = 'fi'; break; case 'French': $langShort = 'fr'; break; case 'Croatian': $langShort = 'hr'; break; case 'Czech': $langShort = 'cs'; break; case 'Swedish': $langShort = 'sv'; break; case 'Estonian': $langShort = 'et'; break; case 'Japanese': $langShort = 'ja'; break; case 'Test': $langShort = 'ja'; break;
            }
            return $langShort;
        }

        $this->view_data['message'] = '';
        foreach ($langFolders as $languageFolder) {
            $this->view_data['message'] .= replaceLines($languageFolder, langShortener($languageFolder), 'messages'); /* Language Folder, API Language name, File name */
            $this->view_data['message'] .= replaceLines($languageFolder, langShortener($languageFolder), 'application'); /* Language Folder, API Language name, File name */
        }
        $this->view_data['return_link'] = '';
        $this->content_view = 'error/error';
        //die();
    }

    public function APIcall($lines = false, $lang = 'en')
    {
    }
}

class TranslateAPI implements YandexAPI
{
    public function APIcall($lines = false, $lang = 'en')
    {
        $key = 'trnsl.1.1.20160221T125934Z.83f2f616f4eeb6a4.bcda4b0c885b3f354219b327743d3461fe98b87b';
        $langDirection = 'en-' . $lang;
        $text = '';

        foreach ($lines as $value) {
            $explodeA = explode("'", $value);
            $text .= '&text=' . urlencode($explodeA[3]);
        }
        $object = remote_get_contents('https://translate.yandex.net/api/v1.5/tr.json/translate?key=' . $key . $text . '&lang=' . $langDirection);

        return json_decode($object);
    }
}
