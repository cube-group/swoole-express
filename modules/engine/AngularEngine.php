<?php
/**
 * Created by PhpStorm.
 * User: linyang
 * Date: 16/9/27
 */
namespace engine;

/**
 * Class AngualrEngine.
 * @package modules\engine
 */
class AngularEngine extends ViewEngine
{
    public function __construct()
    {
        parent::__construct();
    }

    public function render($name, $data = null)
    {
        //remove all comments.
        $htmlData = preg_replace('#<!--[^\!\[]*?(?<!\/\/)-->#', '', parent::getViewContent($name));

        if (empty($data)) {
            return $htmlData;
        } else {
            $angularData = '<script>' . "\t\n";
            $angularData .= 'var cubeAngularJSObject = ' . "\t\n";
            $angularData .= json_encode($data) . ";\t\n";
            $angularData .= '</script>' . "\t\n";

            if (strstr($htmlData, '<head>')) {
                $arr = explode('<head>', $htmlData);
                return $arr[0] . '<head>' . "\t\n" . $angularData . $arr[1];
            } else if (strstr($htmlData, '<html>')) {
                $arr = explode('<html>', $htmlData);
                return $arr[0] . '<html>' . "\t\n" . '<head>' . "\t\n" . $angularData . '</head>' . "\t\n" . $arr[1];
            } else {
                return $angularData . $htmlData;
            }
        }
    }
}
