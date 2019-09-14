#!/usr/bin/php -q
<?php

include_once(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/class.CLI_Script.php');
include_once(Config::$dirIncludes . 'class.Utilities.php');
include_once(Config::$dirIncludes . 'class.Curl.php');

class CLI_images2video extends CLI_Script
{
  public function __construct()
  {
    $this->argMap = array(
      'options' => array(
        'i' => array(
          'required' => TRUE,
          'multiple' => FALSE),
        'd' => array(
          'required' => TRUE,
          'multiple' => FALSE),
        'r' => array(
          'required' => FALSE,
          'multiple' => FALSE),
        'resize' => array(
          'required' => FALSE,
          'multiple' => FALSE),
        'morph' => array(
          'required' => FALSE,
          'multiple' => FALSE),
        'delay' => array(
          'required' => FALSE,
          'multiple' => FALSE),
        'qscale' => array(
          'required' => FALSE,
          'multiple' => FALSE),
        'transitionFormat' => array(
          'required' => FALSE,
          'multiple' => FALSE,
          'default' => 'png'
        ),
      ),
      'flags' => array(
        'del' => 'del',
      )
    );
    parent::__construct();
  }

  public function exec($argv = NULL)
  {
    parent::exec($argv);
    $this->process();
  }

  protected function help()
  {
    $help = "CLI_images2video\n\n";
    $help .= "This command will fetch images from a set of URLs and convert them into a video.\n";
    $help .= "The input is a temporary directory containing a file with a list of image URLs to fetch.\n";
    $help .= "The image files are downloaded into the above temporary directory.\n";
    $help .= "The final video is created in the destination directory.\n\n";
    $help .= "Options\n";
    $help .= "-i: Input file containing the image URL.\n";
    $help .= "-d: Destination path and filename for the final image.\n";
    $help .= "-r: Frame rate of the final video.\n";
    $help .= "-resize: New image size. e.g. '600x800\n";
    $help .= "-morph: Morph the frames, using specified number of frames inbetween each image.\n";
    $help .= "-delay: Mprph the frames, using specified number of frames inbetween each image.\n";
    $help .= "-qscale: specifies the picture quality (1 is the highest, and 32 is the lowest)\n";
    $help .= "-transitionFormat: image format to covert to before encode. Defaults to png\n";
    $help .= "--del: Delete the input directory on completion\n\n";
    $help .= "Example:\n";
    $help .= "CLI_images2video -i /tmp -d /home/foo/bar -resize 400x400 -delay 10 -morph 5 -qscale 1 --del";
    echo $help;
  }

  private function process()
  {
    if (!extension_loaded('Imagick')) {
      Debug::message('Error: please install ImageMagick on the server', 0, Config::$debugCLI, Debug::LOG);
      return;
    }
    if (!file_exists($this->options['i'])) {
      Debug::variable($this->options['i'], 'Error: input directory does not exist', 0, Config::$debugCLI, Debug::LOG);
      return;
    }

    $inputFile = $this->options['i'];
    $pathParts = explode('/', $inputFile);
    $inputFilename = array_pop($pathParts);
    $imagePath = implode('/', $pathParts);
    Debug::variable($inputFile, '$inputFile', 1, Config::$debugCLI, Debug::LOG);
    Debug::variable($imagePath, '$imagePath', 1, Config::$debugCLI, Debug::LOG);

    //get images
    $curl = new Curl();
    $urlFile = fopen($inputFile, 'r');
    $count = 0;
    while (!feof($urlFile)) {
      $url = trim(fgets($urlFile));
      Debug::variable($url, 'url', 1, Config::$debugCLI, Debug::LOG);
      $strParts = explode('.', $url);
      $extension = array_pop($strParts);
      if (!empty($extension)) {
        $outputFilename = str_pad($count++, 5, '0', STR_PAD_LEFT);
        $imageFile = fopen("$imagePath/$outputFilename.$extension", 'w');
        $options = array(CURLOPT_FILE => $imageFile);
        $curl->get($url, $options);
        fclose($imageFile);
      }
    }
    fclose($urlFile);

    Utilities::setAccessRights($imagePath);

    //resize the images & reformat if needed
    $nomask = array('.', '..', '.DS_Store');
    if ($handle = opendir($imagePath)) {
      while (($file = readdir($handle)) !== FALSE) {
        Debug::variable($file, 'file', 1, Config::$debugCLI, Debug::LOG);
        if (strpos($file, $inputFilename) === FALSE && !in_array($file, $nomask)) {
          $fileParts = explode('.', $file);
          $fileName = $fileParts[0];
          $cmd = Config::$convert . " $imagePath/$file";
          $cmd .= isset($this->options['resize']) ? ' -resize ' . $this->options['resize'] : '';
          $cmd .= " $imagePath/$fileName." . Config::$swellnetWamsStandardImageFormat;
          Debug::variable($cmd, 'cmd', 1, Config::$debugCLI, Debug::LOG);
          $output = shell_exec($cmd);
          Debug::message($output, 1, Config::$debugCLI, Debug::LOG);
        }
      }
    }

    //Tweening
    $cmd = Config::$convert . " $imagePath/*." . Config::$swellnetWamsStandardImageFormat;
    $cmd .= isset($this->options['delay']) ? ' -delay ' . $this->options['delay'] : '';
    $cmd .= isset($this->options['morph']) ? ' -morph ' . $this->options['morph'] : '';
    $cmd .= " $imagePath/%05d.final." . $this->options['transitionFormat'];
    Debug::variable($cmd, 'cmd', 1, Config::$debugCLI, Debug::LOG);
    $output = shell_exec($cmd);
    Debug::message($output, 1, Config::$debugCLI, Debug::LOG);

    //make video
    $cmd = Config::$ffmpeg . " -i $imagePath/%05d.final." . $this->options['transitionFormat'];
    $cmd .= !empty($this->options['qscale']) ? ' -qscale ' . $this->options['qscale'] : '';
    $cmd .= !empty($this->options['framerate']) ? ' -r ' . $this->options['framerate'] : '';
    $cmd .= ' ' . $this->options['d'];
    Debug::variable($cmd, 'cmd', 1, Config::$debugCLI, Debug::LOG);
    $output = shell_exec($cmd);
    Debug::message($output, 1, Config::$debugCLI, Debug::LOG);

    //delete images directory
    if (!empty($this->flags['del'])) {
      Debug::variable($imagePath, 'del', 1, Config::$debugCLI, Debug::LOG);
      $it = new RecursiveDirectoryIterator($imagePath, RecursiveDirectoryIterator::SKIP_DOTS);
      $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
      foreach($files as $file) {
        Debug::variable($file, '$file', 1, Config::$debugCLI, Debug::LOG);
        if ($file->isDir()){
          rmdir($file->getRealPath());
        } else {
          unlink($file->getRealPath());
        }
      }
      rmdir($imagePath);
    }
  }
}
