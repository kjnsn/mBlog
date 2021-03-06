<?php

# mBlog v0.4.0 - Copyright 2012 Kaley Main <kaleypoole17@gmail.com>
# Licensed under the GPL version 3 <http://www.gnu.org/licenses/gpl-3.0.html>

require_once 'markdown.php';
require_once 'microtpl.php';

$title = 'mBlog'; # title of your blog
$root = __DIR__ . '/docs/';  # directory with html files


$tpl = new MicroTpl();
$dir = isset($_GET['dir']) ? $_GET['dir'] : null;

if (!is_dir($root . $dir) || strstr($dir, '..'))
  $tpl->error = true;
else{
  foreach(scandir($root . $dir, SCANDIR_SORT_DESCENDING) as $item){
    if (in_array($item, array('.', '..')))
      continue;
    if (is_file($root . $dir . $item)){
      $ext = strtolower(preg_filter('/^.*\.([^\.]+)$/', '\1', $item));
      if (in_array($ext, array('md', 'html'))){
        $text = file_get_contents($root . $dir . $item);
        if ($ext == 'md')
          $text = Markdown($text);
        $tpl->texts[] = array('text' => $text);
      }
    }
    else
      $tpl->dirs[] = array('dir' => $item, 'dirLink' => $dir . $item);
  }
}

$tpl->title = $title;
$tpl->dir = $dir;
ob_start();

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>{title}</title>
    <link href="style.css" rel="stylesheet">
  </head>
  <body>
    <header>{title} /{dir}</header>
    <nav>
      <ul>
        <li><a href="./">~home</a></li>
        {@dirs}
          <li><a href="./?dir={dirLink}/">{dir}</a></li>
        {/dirs}
      </ul>
    </nav>
    {@texts}
      <article>{&text}</article>
    {/texts}
    {?error}
      <article><h1>Error</h1><p>The page you are looking for is not here.</p></article>
    {/error}
  </body>
</html>

<?php $tpl->render(ob_get_clean()); ?>
