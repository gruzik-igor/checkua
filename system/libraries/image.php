<?php

/*
 * Шлях: SYS_PATH/libraries/image.php
 *
 * Робота з зображеннями
 * Версія 1.0.1 (15.06.2013 - додано uploadArray(), wh_size(), cut())
 * Версія 1.0.2 (08.10.2013 - додано preview(), setExtension(), resize(+enlarge))
 * Версія 1.0.3 (16.10.2013 - додано delete(), 25.10.2013 - додано підтримку роботи з розширеннями png i gif)
 * Версія 1.0.3+ (26.10.2013 - додано getExtension(), виправлено preview(), resize(), save())
 * Версія 1.0.4 (27.12.2013 - додано/виправлено у resize() правильне зменшення тільки по ширині зображення)
 */

class Image {

    private $image;
    private $path;
    private $name;
    private $quality = 100;
    private $type;
	private $extension = false;
    private $allowed_ext = array('png', 'jpg', 'jpeg', 'gif');
    private $upload_types = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png');
    private $max_size = 52428;
    private $errors = array();
	
	
	/*
     * Примусове задання розширення зображення
	 * Задавати перед upload(), uploadArray(), save(), перед/після loadImage()
     */
	function setExtension($ext){
		if(in_array($ext, $this->allowed_ext)){
			$this->extension = $ext;
			return true;
		}
		return false;
	}
	
	/*
	 * Вертає поточне розширення зображення
	 */
	function getExtension(){
		if($this->image) return $this->extension;
		return false;
	}

    /*
     * Завантаження зображення з файлової системи
	 * $filepath - адреса папки звідки слід взяти зображення (може включати абсолютний шлях серверу)
	 * $name - назва зображення (без розширення)
	 * $extension - розширення зображення (по замовчуванню jpg)
     */
    function loadImage($filepath, $name = '', $extension = 'jpg'){
		
		if(in_array($extension, $this->allowed_ext) == false) return false;

		if(strlen($filepath) > strlen(SITE_URL)) $filepath = substr($filepath, strlen(SITE_URL));
        if($name != '') $fullpath = $filepath.$name.'.'.$extension;
        else $fullpath = $filepath;
		
		// Функція потребує NULL_PATH (відносно index.php)
		// !Важливо для роботи через піддомен
        @$info = getimagesize($fullpath);
		
		if(!empty($info)){
			
			$this->type = $info[2];
			$this->path = $filepath;
			if($name != '') $this->name = $name;
            else {
                $filepath = explode('/', $filepath);
                $name = end($filepath);
                $this->path = substr($this->path, 0, strlen($name)+1);
                $name = explode('.', $name);
                $this->name = $name[0];
            }
			
			switch ($this->type){
				case '1' :
					$this->image = imagecreatefromgif($fullpath);
					if($this->extension == false) $this->extension = 'gif';
					break;
				case '2' :
					$this->image = imagecreatefromjpeg($fullpath);
					if($this->extension == false) $this->extension = 'jpg';
					break;
				case '3' :
					$this->image = imagecreatefrompng($fullpath);
					if($this->extension == false) $this->extension = 'png';
					break;
			}
			
			return true;
			
		} else return false;
    }

    /*
     * Завантаження зображення зі сторони клієнта
	 * $img_in - назва поля у POST запиті ($_FILES[$img_in])
	 * $img_out - адреса папки куди слід відвантажити зображення (може включати абсолютний шлях серверу)
	 * $name - назва збереженого зображення. Якщо не задано, то оригінальна назва зображення
     */
    function upload($img_in, $img_out, $name = ''){
		
        if(strlen($img_out) > strlen(SITE_URL)) $img_out = substr($img_out, strlen(SITE_URL));
		
        if(is_uploaded_file($_FILES[$img_in]['tmp_name'])){
            $pos = strrpos($_FILES[$img_in]['name'], '.');
            if($pos){
                $name_length = strlen($_FILES[$img_in]['name']) - $pos;
                $ext = strtolower(substr($_FILES[$img_in]['name'], $pos + 1, $name_length));
                if(in_array($ext, $this->allowed_ext)){
                    if(in_array($_FILES[$img_in]['type'], $this->upload_types)){
                        $size = $_FILES[$img_in]['size'] / 1024;
                        if($size <= $this->max_size){
							if($this->extension) $ext = $this->extension;
							if(strlen($img_out) > strlen(SITE_URL)) $img_out = substr($img_out, strlen(SITE_URL));
							if($name == '') $name = stripslashes(substr($_FILES[$img_in]['name'], 0, $pos - 1));
                            $path = $img_out.$name.'.'.$ext;
                            move_uploaded_file($_FILES[$img_in]['tmp_name'], $path);
                            $this->loadImage($img_out, $name, $ext);
                        } else {
                            array_push($this->errors, 'Розмір файлу не повинен перевищувати '.$this->max_size);
                            return false;
                        }
                    } else {
                        array_push($this->errors, 'Такий тип файлу  не підтримується.');
                        return false;
                    }
                } else {
                    array_push($this->errors, 'Таке розширення не підтримується.');
                    return false;
                }
            } else {
                array_push($this->errors, 'Файл повинен мати розширення.');
                return false;
            }
        }
    }
	
	/*
     * Обробка масового (групового) завантаження зображень зі сторони клієнта
	 * $img_in - назва поля у POST запиті ($_FILES[$img_in])
	 * $i - номер елементу у масиві $_FILES[$img_in]['tmp_name'][$i]
	 * $img_out - адреса папки куди слід відвантажити зображення (може включати абсолютний шлях серверу)
	 * $name - назва збереженого зображення. Якщо не задано, то оригінальна назва зображення
     */
	function uploadArray($img_in, $i, $img_out, $name = ''){
        
		if(strlen($img_out) > strlen(SITE_URL)) $img_out = substr($img_out, strlen(SITE_URL));
		
        if(is_uploaded_file($_FILES[$img_in]['tmp_name'][$i])){
            $pos = strrpos($_FILES[$img_in]['name'][$i], '.');
            if($pos){
                $name_length = strlen($_FILES[$img_in]['name'][$i]) - $pos;
                $ext = strtolower(substr($_FILES[$img_in]['name'][$i], $pos + 1, $name_length));
                if(in_array($ext, $this->allowed_ext)){
                    if(in_array($_FILES[$img_in]['type'][$i], $this->upload_types)){
                        $size = $_FILES[$img_in]['size'][$i] / 1024;
                        if($size <= $this->max_size){
							if($this->extension) $ext = $this->extension;
							if(strlen($img_out) > strlen(SITE_URL)) $img_out = substr($img_out, strlen(SITE_URL));
							if($name == '') $name = stripslashes(substr($_FILES[$img_in]['name'], 0, $pos - 1));
                            $path = $img_out.$name.'.'.$ext;
                            move_uploaded_file($_FILES[$img_in]['tmp_name'][$i], $path);
                            $this->loadImage($img_out, $name, $ext);
                        } else {
                            array_push($this->errors, 'Розмір файлу не повинен перевищувати '.$this->max_size);
                            return false;
                        }
                    } else {
                        array_push($this->errors, 'Такий тип файлу  не підтримується.');
                        return false;
                    }
                } else {
                    array_push($this->errors, 'Таке розширення не підтримується.');
                    return false;
                }
            } else {
                array_push($this->errors, 'Файл повинен мати розширення.');
                return false;
            }
        }
    }
	
	/*
	 * Функція повертає розміри (розширення) зображення у px
	 */ 
	function wh_size(){
		if($this->image){
			$size = array();
			$size['width'] = imagesx($this->image);
			$size['height'] = imagesy($this->image);
			$size['w'] = $size['width'];
			$size['h'] = $size['height'];
			return $size;
		} return null;
	}
	
	/*
     * Створення мініатюри зображення
	 * Функція змінює розміри зображення до максимально можливого, опісля центрує та обрізає зображення. На виході мініатюра заданого розміру.
	 * $quality - якість кінцевого зображення після обробки у відсотках
     */
    function preview($width, $height, $quality = 100){
		if($this->image){
			
			$this->quality = $quality;
			
			$src_w = imagesx($this->image);
			$src_h = imagesy($this->image);

			// if($width < $src_w && $height < $src_h){
				$w = $width;
				$h = $height;
				$ratio = $src_w / $src_h;
				if($width / $ratio < $height) $w = round($height * $ratio) + 1;
				// elseif($height / $ratio > $width) $h = round($width / $ratio);
				else $h = round($width / $ratio) + 1;
				
				$this->resize($w, $h, $quality, true);

				$src = $this->image;
				$w = imagesx($src);
				$h = imagesy($src);
				$this->image = imagecreatetruecolor($width, $height);
				
				$src_x = 0; $src_y = 0;
				if($w > ($width - 1) && $w <= ($width + 1)) $src_y = ($h - $height) / 2;
				else $src_x = ($w - $width) / 2;
				
				imagecopy ($this->image, $src, 0, 0, $src_x, $src_y, $width, $height);
				
				imagedestroy($src);
				return true;
			// }
		}
        return false;
    }

    /*
     * Зміна розмірів зображення
     */
    function resize($width, $max_height = 0, $quality = 100, $enlarge = false) {
		if($this->image){
			
			$this->quality = $quality;
			$src = $this->image;
			$src_w = imagesx($src);
			$src_h = imagesy($src);

			if($enlarge || ($width < $src_w && $max_height < $src_h)){
				$ratio;
				if($src_w < $src_h && $max_height > 0)
					$ratio = $src_h / $max_height;
				else $ratio = $src_w / $width;
				$dest_w = round($src_w / $ratio);
				$dest_h = round($src_h / $ratio);
				$d_h = ($dest_h > $max_height && $max_height > 0) ? $max_height : $dest_h;
				$this->image = imagecreatetruecolor($dest_w, $d_h);
				imagecopyresampled($this->image, $src, 0, 0, 0, 0, $dest_w, $dest_h, $src_w, $src_h);
				
				imagedestroy($src);
				return true;
			}
			
		}
        return false;
    }
	
	 /*
     * Обрізання зображення
     */
    function cut($width, $height, $red = 0, $green = 0, $blue = 0) {
		if($this->image){
			
			$src = $this->image;
			$this->image = imagecreatetruecolor($width, $height);
			if($red > 0 || $green > 0 || $blue > 0){
				$color = imagecolorallocate ($this->image, $red, $green, $blue);
				imagefill($this->image, 0, 0, $color);
			}
			imagecopy($this->image, $src, 0, 0, 0, 0, $width, $height);
			imagedestroy($src);
			return true;
			
		}
        return false;
    }

    /*
     * Зберігання отриманого зображення
     */
    function save($path = '', $prefix = ''){
		if($this->image){
			
			if($path != '' && strlen($path) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
            else $path = $this->path;
			$name = ($prefix != '') ? $prefix.'_'.$this->name.'.'.$this->extension : $this->name.'.'.$this->extension;
			
			if($this->extension == 'gif'){
				if(imagegif($this->image, $path.$name)){
					imagedestroy($this->image);
					return true;
				}
			}
			if($this->extension == 'jpg'){
				if(imagejpeg($this->image, $path.$name, $this->quality)){
					imagedestroy($this->image);
					return true;
				}
			}
			if($this->extension == 'png'){
				if(imagepng($this->image, $path.$name)){
					imagedestroy($this->image);
					return true;
				}
			}
			
			imagedestroy($this->image);
		}
        return false;
    }
	
	/*
     * Видалення зображення
     */
	function delete($path = ''){
		$path = ($path != '') ? $path : $this->path;
		if(strlen($path) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
		if(file_exists($path)){
			if(unlink($path)) return true;
		}
		return false;
	}

    /*
     * Отримує помилки
     */
    function getErrors($open_tag = '<p>', $closed_tag = '</p>'){
        $errors = '';
        foreach ($this->errors as $error){
            $errors .= $open_tag.$error.$closed_tag;
        }

        return $errors;
    }
}

?>
