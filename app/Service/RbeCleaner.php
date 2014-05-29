<?php namespace App\Service;

class RbeCleaner {

	private $errors = array();

	public function prepareInput($input) {
		$input = $this->cleanInputBeforeDecoding($input);
		$input = $this->decodeInput($input);
		$input = $this->cleanInputAfterDecoding($input);
		return $input;
	}

	private function cleanInputBeforeDecoding($input) {
		$input = preg_replace('#<p class=(MsoNormal|MsoPlainText) [^>]+>#', '<p class=MsoNormal>', $input);
		$input = preg_replace('#font-size: *[^;>]+;#', '', $input);
		$input = strtr($input, array(
			',"serif"' => '',
			',"sans-serif"' => '',
			'font-family:Modern' => '',
			'color:black' => '',
			'color:yellow' => '',
		));
		$input = str_replace("\n", ' ', $input);
		$input = str_replace(' ', ' ', $input); // turn nbsp into a normal space
		$input = preg_replace('#<i> +</i>#', '', $input);
		$input = str_replace('</p>', "</p>\n", $input);
		$input = preg_replace('#\n +#', "\n", $input);
		$input = str_replace(' </span><span', '</span> <span', $input);
		$input = preg_replace('# lang=(BG|RU|EN-US)#', '', $input);
		$input = str_replace("<span style='position:relative;top:-6.0pt'>,</span>", '’', $input);
		$input = preg_replace("#<span style='position: *relative; *top:-[5678]\.0pt'>(.+)</span>#U", '<sup>$1</sup>', $input);
		$input = preg_replace("#<span style='position: *relative; *top:[67]\.0pt'>(.+)</span>#U", '$1', $input);
		$input = preg_replace('#<span style=\'[^\'>]+\'>&nbsp;</span>#U', '', $input); // думи с разредка
		$input = preg_replace('#<span> +</span>#', '', $input);
		$input = preg_replace('#<span style=\'letter-spacing: *[\d.-]+pt\'>(.*)</span>#U', '$1', $input);
		$input = preg_replace('#<span style=\'font-family:"TmsCyrNewA";position:relative; *top:-2\.0pt\'>(.+)</span>#U', '<sup>$1</sup>', $input);
		$input = preg_replace('#<span[^>]+> +</span>#', ' ', $input);
		$input = preg_replace('#<span style=\' *position: *relative; *top:-?[23]\.0pt[^>]*\'>(.+)</span>#U', '<sup>$1</sup>', $input);
		$input = preg_replace('#<span style=\' *position: *relative; *top:[23]\.0pt\'>(.*)</span>#U', '<sub>$1</sub>', $input);
		$input = preg_replace('#<span style=\' *letter-spacing: *[^;>]+\'>([^>]+)</span>#', '$1', $input);
		$input = preg_replace('#<span style=\'font-size: *[^>]+\'>(.*)</span>#U', '$1', $input);
		$input = str_replace("style=' ", "style='", $input);
		$input = str_replace(" style=''", '', $input);
		$input = preg_replace('#<span>(.+)</span>#U', '$1', $input);
		$input = preg_replace('#<span[^>]+></span>#', '', $input);
		$input = trim($input);
		return $input;
	}
	private function cleanInputAfterDecoding($input) {
		$input = preg_replace('# (</\w+>)([<А-Яа-я])#u', '$1 $2', $input); // [Мат. </i>Сума] --> [Мат.</i> Сума]
		$input = preg_replace('#(<[bi]>) #', ' $1', $input);
		$input = preg_replace('#,(</[bi]>) #', '$1, ', $input);
		$input = str_replace('</b><b>', '', $input);
		$input = preg_replace('#<span [^>]+></span>#', '', $input);
		$input = preg_replace('#<span style=\'font-size:[\d.]+pt\'>(.+)</span>#U', '$1', $input);
		$input = preg_replace('#<i> *</i>#', '', $input);
		$input = strtr($input, array(
			#"'" => '’',
			', ‑</b>' => '</b>, ‑',
			'<b></b>' => '',
			'<i>,</i>' => ',',
			'</i> <i>' => ' ',
			'<sup></sup>' => '',
		));
		$input = preg_replace('#<sup> +</sup>#', ' ', $input);
		$input = preg_replace('#<p> +<b>#', '<p><b>', $input);
		$input = preg_replace('#<p><b> +#', '<p><b>', $input);
		return $input;
	}

	private function decodeInput($input) {
		// extract and save custom font blocks in a separate array
		$reForFonts = '#<span style=\' *font-family:[^\']+\'>.+</span>#Ums';
		$placeholder = 'CUSTOM_FONT_PLACEHOLDER';
		if (preg_match_all($reForFonts, $input, $savedContent)) {
			$savedContent = $savedContent[0];
			$input = preg_replace($reForFonts, $placeholder, $input);
		}

		// fix base cyrillic
		$input = preg_replace_callback('#<p class=MsoNormal>(.+)</p>#Ums', function($matches) {
			return '<p>' . $this->fixCyrillic($matches[1]) . '</p>';
		}, $input);

		// put back custom font blocks
		while (strpos($input, $placeholder) !== false) {
			$input = preg_replace("#$placeholder#", array_shift($savedContent), $input, 1);
		}

		// fix custom font blocks
		$input = preg_replace_callback('#<span style=\' *font-family: *"TmsCyrNewA?"[^\'>]*\'>(.+)</span>#Ums', function($matches) {
			return $this->fixCyrillic($matches[1]) ?: "[TODO convert cyrillic=$matches[1]]";
		}, $input);

		$input = preg_replace_callback('#<span style=\' *font-family: *"(TmsGk Old|TmsGk)"[^\'>]*\'>(.+)</span>#Ums', function($matches) {
			return $this->fixGreek($matches[2]) ?: "[TODO convert greek=$matches[2]]";
		}, $input);
		$input = preg_replace_callback('#<span style=\' *font-family: *"TmsTr"[^\'>]*\'>(.+)</span>#Ums', function($matches) {
			return $this->fixTurkish($matches[1]) ?: "[TODO convert turkish=$matches[1]]";
		}, $input);
		$input = preg_replace_callback('#<span style=\' *font-family: *"(TmsEe|TmsBaltic|TmsCyr|Tms New Roman|Times New Roman|WP Greek Courier|MT Extra|Alexei Copperplate|Hebar|Etymolog1)"[^\'>]*\'>(.+)</span>#Ums', function($matches) {
			return $matches[2]; // no conversion needed
		}, $input);

		$input = strtr($input, array(
			'&#x0091;' => '’',
			'&#x0092;' => '’',
			'&#768;' => '`',
			'&#769;' => '´',
			' Ј ' => 'и`',
		));
		$input = html_entity_decode($input, ENT_QUOTES, 'UTF-8');
		$input = $this->fixAccents($input);

		return $input;
	}

	private function fixSource($input) {
		return $this->iconv('utf-8', 'windows-1252', $input);
	}

	private function fixCyrillic($input) {
		$input = $this->iconv('windows-1251', 'utf-8', $this->fixSource($input));
		$input = strtr($input, array(
			'&#152;' => 'е`',
			'&#1109;' => 'я`',
		));
		return $input;
	}
	private function fixGreek($input) {
		return $this->iconv('windows-1253', 'utf-8', $this->fixSource($input));
	}

	private function fixTurkish($input) {
		return $this->iconv('windows-1254', 'utf-8', $this->fixSource($input));
	}

	private function fixAccents($input) {
		return strtr($input, array(
			'Ђ' => 'А`',
			'Ћ' => 'О`',
			'Љ' => 'Е`',
			'ђ' => 'Ъ`',
			'Њ' => 'И`',
			'Џ' => 'У`',
			'ћ' => 'Ю`',
			'џ' => 'Я`',
			'Ґ' => 'о`',
			'і' => 'ъ`',
		));
	}

	private function iconv($inCharset, $outCharset, $string) {
		$convertedString = iconv($inCharset, $outCharset, $string);
		if ($convertedString === false) {
			$this->errors[] = "iconv ($inCharset -> $outCharset)\nInput:\n$string";
			return '';
		}
		return $convertedString;
	}
}
