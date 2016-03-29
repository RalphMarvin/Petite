<?php 
/**
 * Petite - This is a PHP source code file minifier. Petite strips out
 * comments and whitespaces in your php files making them lighter in size.
 * @author Ralph Marvin Addo
 */
 
 class PhpSourceCodeMinifier
 {
     /**
      * @var to contain the minified file content.
      * @access private.
      */
     private $minifiedSource = null;
     
     /**
      * Displays the current progress of an operation
      * @param string $str
      * @access private
      */
     private function Report( $str )
     {
         echo( nl2br( $str."\n", false ) );
     }
     
     /**
      * Strips both whitespace and comments from a php source file
      * and returns the result. i.e, the minified version
      * @param string $phpFile :-   A php file.
      * @access private
      * @return string
      */
     private function MinifySource( $phpFile )
     {
         $this->Report( $phpFile.' is being minified ...' );
         
         $phpFile = php_strip_whitespace( $phpFile );
         
         $this->Report( $phpFile. ' has been minified!' );
         
         return $phpFile;
     }
     
     /**
      * Determines whether a file is a php file or not.
      * @param string $fileName :-  The file name.
      * @access private
      * return boolean
      */
     private function IsFilePhp( $fileName )
     {
         $extension = pathinfo( $fileName, PATHINFO_EXTENSION );
         
         switch( $extension )
         {
             case 'php':
                return true;
             default:
                return false;
         }
     }
     
     /**
      * Saves a php file to the path specified.
      * @param string $fileContent
      * @param string $fileName
      * @param string $path
      * @access private
      */
     private function SaveToNewFile( $fileContent, $fileName, $path )
     {
         $this->Report( $fileName.' is saving ..' );
         
         $fileName = pathinfo( $fileName, PATHINFO_FILENAME );
         file_put_contents( $path.'/'.$fileName.'.php', $fileContent );
         
         $this->Report( $fileName.' has been saved!' );
     }
     
     /**
      * Overwrites a specified php files content.
      * @param string $fileContent
      * @param string $fileName
      * @access private
      */
     private function OverwriteFile( $fileContent, $fileName )
     {
         $this->Report( $fileName.' is saving ..' );
         
         file_put_contents( $fileName, $fileContent );
         
         $this->Report( $fileName.' has been saved!' );
     }
     
     /**
      * This method handles checking the passed parameters and minifying the php file
      * according to the parameters. NOTE: This only minifies just a single file.
      * @param string $file
      * @param boolean $overwrireFile
      * @param string $outPath
      * @access public
      */
     public function MinifyFile( $file, $overwriteFile = false, $outPath = null )
     {
         if ( is_array( $file ) || is_dir( $file ) )
         {
             exit( $file.' is either an array or a directory ...' );
         }
         else
         {
             if ( $overwriteFile == true )
             {
                 if ( $outPath == null )
                 {
                     if ( $this->IsFilePhp( $file ) )
                     {
                         //minify source and oerwrire file
                         $this->minifiedSource = $this->MinifySource( $file );
                     
                         //now we overwrite unminified version
                         $this->OverwriteFile( $this->minifiedSource, $file );
                     }
                     else
                     {
                         $this->Report( $file.' is not a php file, skipping ...' );
                     }
                 }
                 else
                 {
                     //check if the outPath is a directory
                     if ( is_dir( $outPath ) )
                     {
                         if ( $this->IsFilePhp( $file ) )
                         {
                             //minify source
                             $this->minifiedSource = $this->MinifySource( $file );
                         
                             //save file to the path specified
                             //NOTE: Use the unminifed versions file name s the filename
                             //for the minifed version.
                             $fileName = pathinfo( $file, PATHINFO_FILENAME );
                             $this->SaveToNewFile( $this->minifiedSource, $fileName.'.php', $outPath );
                         }
                         else
                         {
                             $this->Report( $file.' is not a php file, skipping ...' );
                         }
                     }
                     else
                     {
                         exit( $outPath.' is not a directory' );
                     }
                 }
             }
             //OverwriteFile is set to false
             else
             {
                 if ( $outPath == null )
                 {
                     //minify source and save it as a new file
                     $this->minifiedSource = $this->MinifySource( $file );
                     
                     //the new name of the minified file
                     $fileName = pathinfo( $file, PATHINFO_FILENAME );
                     $newName = $fileName.'.min.php';
                     
                     //save file
                     $this->SaveToNewFile( $this->minifiedSource, $newName, pathinfo( $file, PATHINFO_DIRNAME ) );
                 }
                 else
                 {
                     if ( is_dir( $outPath ) )
                     {
                         if ( $this->IsFilePhp( $file ) )
                         {
                             //minify source
                             $this->minifiedSource = $this->MinifySource( $file );
                         
                             //save file to the path specified
                             //but NOTE: the minified files will bear
                             //new name with .min.php
                             $fileName = pathinfo( $file, PATHINFO_FILENAME );
                             $newName = $fileName.'.php';
                         
                             $this->SaveToNewFile( $this->minifiedSource, $newName, $outPath );
                         }
                         else
                         {
                             $this->Report( $file.' is not a php file, skipping ...' );
                         }
                     }
                     else
                     {
                         exit( $outPath.' is not a directory' );
                     }
                 }
             }
         }
     }
     
     /**
      * Minifies several php files according to the parameters passed along.
      * @param array $files
      * @param boolean $overwriteFiles
      * @param string $outPath
      * @access public
      */
     public function MinifyMultipleFiles( $files = array(), $overwriteFiles = false,  $outPath = null )
     {
         if ( is_array( $files ) )
         {
             foreach( $files as $file )
             {
                 $this->MinifyFile( $file, $overwriteFiles, $outPath );
             }
         }
         else
         {
             exit( $files.' is not an array' );
         }
     }
     
     /**
      * Minifies all php files in a given directory also according to the 
      * parameters passed along. NOTE: This method wouldnt minify a sub
      * directory in the directory specified. Just the directory given.
      * @param string $filePath
      * @param boolean $overwriteFiles
      * @param string $outPath
      * @access public
      */
     public function MinifyFilesInDirectory( $filePath, $overwriteFiles = false, $outPath = null )
     {
         if ( is_dir( $filePath) )
         {
             //return all the file in the directory
             $phpFiles = array_diff( scandir( $filePath ), array( '..','.' ) );
             
             foreach( $phpFiles as $file )
             {
                 $this->MinifyFile( $filePath.'/'.$file, $overwriteFiles, $outPath );
             }
         }
         else
         {
             exit( $filePath.' is not a directory' );
         }
     }
 }
 
?>