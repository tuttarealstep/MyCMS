<?php
/*                     *\
|	MYCMS - TProgram    |
\*                     */
define('MY_CMS_PATH', true);
define("LOADER_LOAD_PAGE", false);
include '../../../../src/Bootstrap.php';

class TreeView
{
    private $files;
    private $folder;

    function __construct($path)
    {

        if (file_exists($path)) {
            if ($path[ strlen($path) - 1 ] == '/')
                $this->folder = $path;
            else
                $this->folder = $path . '/';

            $this->dir = opendir($path);
            while (($file = readdir($this->dir)) != false)
                $this->files[] = $file;
            closedir($this->dir);
        }
    }

    function create_tree()
    {
        if (count($this->files) > 2) { /* First 2 entries are . and ..  -skip them */
            natcasesort($this->files);
            $list = '<ul class="filetree" style="display: none;">';
            // Group folders first
            foreach ($this->files as $file) {
                if (file_exists($this->folder . $file) && $file != '.' && $file != '..' && is_dir($this->folder . $file)) {
                    $list .= '<li class="folder collapsed"><a href="#" rel="' . htmlentities($this->folder . $file) . '/">' . htmlentities($file) . '</a></li>';
                }
            }
            // Group all files
            foreach ($this->files as $file) {
                if (file_exists($this->folder . $file) && $file != '.' && $file != '..' && !is_dir($this->folder . $file)) {
                    $ext = preg_replace('/^.*\./', '', $file);
                    $list .= '<li class="file ext_' . $ext . '"><a href="#" rel="' . htmlentities($this->folder . $file) . '">' . htmlentities($file) . '</a></li>';
                }
            }
            $list .= '</ul>';

            return $list;
        }

        return null;
    }
}

global $my_users, $my_db;
hideIfStaffNotLogged();
if (staffLoggedIn() && isset($_POST['dir'])) {
    $user_rank = $my_users->getInfo($_SESSION['staff']['id'], 'rank');
    if ($user_rank < 3) {
        return;
    }

    //if($_POST['dir'] == MY_THEME)
    //{
    $_POST['dir'] = "../Theme/" . $_POST['dir'];
    //}
    $path = $_POST['dir'];
    $tree = new TreeView($path);
    echo $tree->create_tree();
}
?>
