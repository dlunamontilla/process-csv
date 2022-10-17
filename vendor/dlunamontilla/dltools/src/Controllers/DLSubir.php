<?php
namespace DLTools\Controllers;

use \tpmanc\imagick\Imagick;

/**
 * 
 * @package DLSubir
 * @author David E Luna M <davidlunamontilla@gmail.com>
 * @version 1.0.0
 * @license MIT
 * 
 * Herramienta para subir archivos de forma amigable. Por ahora, solo admite imágenes
 * por defecto.
 * 
 * IMPORTANTE: Esta herramienta depende de Imagick para crear thumbnails, por lo tanto,
 * se debe instalar antes utilizarla.
 * 
 */

class DLSubir {
  # PROPIEDADES PROTEGIDAS:
  protected $type = [];
  protected $ruta;

  # CONSTRUCTOR: 
  public function __construct( array $info = ["ruta" => "./uploads", "tipo" => "imagen"] ) {
    $this -> ruta = (string) $info["ruta"];

    switch ( $info["tipo"] ) {
      case "imagen":

        $this -> type = [
          IMAGETYPE_PNG => (string) "image/png",
          IMAGETYPE_JPEG => (string) "image/jpeg",
          IMAGETYPE_GIF => (string) "image/gif",
          IMAGETYPE_BMP => (string) "image/bmp"
        ];

        break;
    }
  }

  # MÉTODOS PROTEGIDOS:

  # Quitar espacios en blanco entre palabras y caracteres:
  protected function limpiarCadena( string $cadena = "" ) {
    if ( empty(trim($cadena)) )
      return;

    # Quitar espacios en blanco al principio y final de la cadena:
    $cadena = trim($cadena);

    # Reemplazar los espacios en blanco con guiones bajos:
    $reemplazar = [
      "patron" => [
        0 => "/\s/",
        1 => "/\-/"
      ],

      "destino" => [
        0 => "_",
        1 => "_"
      ]
    ];

    $cadena = preg_replace( $reemplazar['patron'], $reemplazar['destino'], $cadena );

    # Devolver la cadena limpiar;
    return (string) $cadena;
  }

  # Relación de aspecto de las imágenes:
  protected function aspect( string $_destino, int $_width ) : array {
    if ( empty(trim($_destino)) || !file_exists($_destino) )
      return ["width" => (int) 100, "height" => (int) 100];

    # Obtener los atributos de las imágenes para redimensionarlas:
    list($ancho, $alto, $tipo, $attributos) = getimagesize($_destino);
    $aspect = $ancho / $alto;

    # Devolver un array con las nuevas dimensiones de la imagen:
    return [
      "width" => (int) $_width,
      "height" => (int) $_width / $aspect
    ];
  }

  # Validar las imágenes:
  protected function validar( string $tmp_name = "" ) : bool {
    if ( empty(trim($tmp_name)) )
      return false;

    foreach ( $this -> type as $key => $formato ) {
      if ( (int) $key === (int) exif_imagetype($tmp_name) )
        return true;
    }
  }

  # Cambiar el tamaño de la imagen:
  protected function vistaPrevia( string $imagen = "", $width = 100, $height = 100 ) : string {

    if (
      !class_exists('Imagick') ||
      empty(trim($imagen))
    ) {
      return "Sin vista previa:\nSi utilizas «Ubuntu» o distribuciones similares puede instalarlo escribiendo \"sudo apt install php-imagick\". Si utilizas otros sistemas operativos puede visitar => https://imagemagick.org/script/download.php ";
    }

    if ( !file_exists( $imagen ) )
      return "";

    if ( !$this -> validar( $imagen ) )
      return "";

    $vistaPrevia = new Imagick( $imagen );
    $vistaPrevia->resize((int) $width, (int) $height);

    # Crear el directorio si no existe:
    $dir = $this->ruta . "/" . date("Y/m") . "/thumbnail";

    if ( !file_exists($dir) )
      mkdir($dir, 0755, true);


    # Separar el archivo por extensión:
    $ext = explode(".", $imagen);
    $destino = $dir . "/" . sha1($imagen . date("Yis")) . "." . end($ext);
  
    # Crear una copia de la imagen original con otras dimensiones:
    $vistaPrevia->saveTo($destino);

    return $destino;
  }

  # PUBLIC:
  public function archivo( string $_name = "", $_width = 400 ) {
    if ( empty(trim($_name)) )
      return;

    if ( !array_key_exists( $_name, $_FILES ))
      return;

    if ( !is_array( $this -> type ) || count( $this -> type ) < 1 )
      return;

    $name = $_FILES[$_name]['name'];
    $tmp_name = $_FILES[$_name]['tmp_name'];

    $multiple = (boolean) is_array( $name );
    
    // Modo de envío simple:
    if ( !$multiple ) {

      # Devuelve true si la validación tuvo éxito:
      $valido = $this -> validar( $tmp_name );

      # Si el formato de archivo es válido se procede a moverse
      # hacia el directorio de subida de archivos:

      # Se verifica si se validó el formato del archivo:
      if ( !$valido )
        return [
          "info" => "El archivo no es válido"
        ];

      # Se verifica que efectivamente se haya subido el archivo:
      if ( !is_uploaded_file( $tmp_name  ) )
        return [];
      
      $destino = $this -> ruta . "/" . date("Y/m");

      if ( !file_exists($destino) )
        if ( ! @mkdir( $destino, 0755, true ) )
          return [
            0 => [
              "info" => "No cuenta con suficientes permisos para crear y  enviar archivos al directorio de destino",
              "error" => (boolean) true
            ]
          ];

      # Limpiar la cadena de caracteres antes de enviar:
      $destino = $destino . "/" . $this -> limpiarCadena($name);

      # Mover el archivo al directorio de subida:
      $subido = move_uploaded_file( $tmp_name, $destino);
      if ( $subido ) {

        setcookie("mensaje", "Archivo subido correctamente", time() + (60 * 60 * 24 * 365), "/");

        # Relación de aspecto de la imagen:
        $aspect = $this -> aspect( $destino, $_width );

        # Evaluar si destino existe:
        if ( !file_exists($destino) )
          return[
            [
              "info" => (string) "El directorio \"$destino\" no existe",
              "error" => (boolean) true
            ]
          ];

        # Devuelve un array con dos rutas, a la vez que crea una vista previa:
        $ficheros = [];

        array_push( $ficheros, [
          "fichero" => "$destino",
          "thumbnail" => $this -> vistaPrevia( $destino, $aspect["width"], $aspect["height"] ),
          "info" => "No se encontraron errores",
          "error" => (boolean) false
        ]);

        return $ficheros;
      }

      setcookie("mensaje", "No se pudo subir el archivo", time() + (60 * 60 * 24 * 365), "/");

      return [];
    }

    # Modo múltiple:
    if ( $multiple ) {
      $datos = [];

      foreach ( $tmp_name as $key => $archivo ) {
        $valido = $this -> validar ( $archivo );

        if ( $valido && is_uploaded_file($archivo) ) {
          $destino = $this -> ruta . "/" . date("Y/m");
          
          # Si no existe la ruta de destino, se deberá crear:
          if ( !file_exists($destino) )
            if ( ! @mkdir( $destino, 0755, true ) )
            return [
              0 => [
                "info" => "No cuenta con suficientes permisos para crear y  enviar archivos al directorio de destino",
                "error" => (boolean) true
              ]
            ];

          # Depurar la ruta de destino:
          $destino = $destino . "/" . $this -> limpiarCadena($name[$key]);

          # Mover el archivo al directorio de subida y confirmar su proceso. Si
          # se confirma se creará un subdirectorio con las vistas previas:
          if ( move_uploaded_file($archivo, $destino) ) {
            setcookie("mensaje", "Archivo subido correctamente", time() + (60 * 60 * 24 * 365), "/");

            # Evaluar si destino existe:
            if ( !file_exists($destino) )
              return [];
              
            # Relación de aspecto de las imágenes en miniaturas:
            $aspect = $this -> aspect( $destino, $_width );

            $thumbnail = $this -> vistaPrevia( $destino, $aspect["width"], $aspect["height"] );
            $error = false;

            # Devolver un array, a la vez que se crean la vista previa:
            array_push( $datos, [
              "fichero" => $destino,
              "thumbnail" => $thumbnail,
              "info" => "El archivo «" . $this -> limpiarCadena( $name[$key]) . "» fue enviado correctamente",
              "error" => (boolean) $error
            ]);
          }
        }
      }

      if ( count($datos) < 1 )
        return [];

      return $datos;
    }
  }
 }
