<?php
class CTextProcessor {
	public static function parse($a_szContent, $a_bOnlyText = true) {
		// $a_szContent = self::autolink ($a_szContent);
		$a_szContent = self::syntaxColoring ($a_szContent);
		//if ($a_bOnlyText)
		//	return nl2br($a_szContent);
		return $a_szContent;
	}
	public static function syntaxColoring ($a_szContent) {
		$new = array ();
		$is_code = false;
		$is_html = false;
		$lines = explode ("\n", $a_szContent);
		for ($i = 0; $i < count ($lines) - 1; $i++) {
			$line = $lines[$i];
			if (strpos ($line, "[code") !== FALSE)
				$is_code = true;
			if (strpos ($line, "[html") !== FALSE)
				$is_html = true;
			// if (!$is_html) $line = htmlspecialchars ($line);
			if (!$is_code) $line = $line = self::autolink ($line) . "<br />";
			else $line = htmlspecialchars ($line);
			array_push ($new, $line);
			if (strpos ($line, "[/code]") !== FALSE)
				$is_code = false;
			if (strpos ($line, "[/html]") !== FALSE)
				$is_html = false;
		}
		array_push ($new, self::autolink ($lines[count ($lines) - 1]));
		$a_szContent = implode ("\n", $new);
		$a_szContent = str_replace ("[code lang=&quot;", "[code lang=\"", $a_szContent);
		$a_szContent = str_replace ("&quot;]", "\"]", $a_szContent);

		$a_szContent = preg_replace ("/\[\/code\]/", "</pre>", preg_replace ("/\[code\s+lang=\"(\w+)\"\s*\]/", "<pre class=\"brush: \\1;\">", $a_szContent));
		$a_szContent = preg_replace ("/\[\/html\]/", "", preg_replace ("/\[html\s*\]/", "", $a_szContent));

		$a_szContent = str_replace ("[{code", "[code", $a_szContent);
		$a_szContent = str_replace ("[{/code", "[/code", $a_szContent);
		$a_szContent = str_replace ("[{html", "[html", $a_szContent);
		$a_szContent = str_replace ("[{/html", "[/html", $a_szContent);
		return $a_szContent;
		//return preg_replace ("/\[\/code\]/", "</textarea>",
		//	preg_replace ("/\[code lang=\"(\w+)\"\]/", "<textarea class=\"\\1\" name=\"code\" cols=\"60\" rows=\"10\">", $a_szContent));
	}

	# 문서 내용에 있는 URL들을 찾아내어 자동으로 링크를 구성해주는 함수
	#
	# preg_replace  - 펄 형식의 정규표현식을 이용한 치환
	#                 http://www.php.net/manual/function.preg-replace.php
	public static function autolink($str) {
		global $agent,$rmail;

		$regex['file'] = "gz|tgz|tar|gzip|zip|rar|mpeg|mpg|exe|rpm|dep|rm|ram|asf|ace|viv|avi|mid|gif|jpg|png|bmp|eps|mov";
		$regex['file'] = "(\.({$regex['file']})\") TARGET=\"_blank\"";
		$regex['http'] = "(http|https|ftp|telnet|news|mms):\/\/(([\xA1-\xFEa-z0-9:_\-]+\.[\xA1-\xFEa-z0-9,:;&#=_~%\[\]?\/.,+\-]+)([.]*[\/a-z0-9\[\]]|=[\xA1-\xFE]+))";
		$regex['mail'] = "([\xA1-\xFEa-z0-9_.-]+)@([\xA1-\xFEa-z0-9_-]+\.[\xA1-\xFEa-z0-9._-]*[a-z]{2,3}(\?[\xA1-\xFEa-z0-9=&\?]+)*)";

		# &lt; 로 시작해서 3줄뒤에 &gt; 가 나올 경우와
		# IMG tag 와 A tag 의 경우 링크가 여러줄에 걸쳐 이루어져 있을 경우
		# 이를 한줄로 합침 (합치면서 부가 옵션들은 모두 삭제함)
		$src[] = "/<([^<>\n]*)\n([^<>\n]+)\n([^<>\n]*)>/i";
		$tar[] = "<\\1\\2\\3>";
		$src[] = "/<([^<>\n]*)\n([^\n<>]*)>/i";
		$tar[] = "<\\1\\2>";
		$src[] = "/<(A|IMG)[^>]*(HREF|SRC)[^=]*=[ '\"\n]*({$regex['http']}|mailto:{$regex['mail']})[^>]*>/i";
		$tar[] = "<\\1 \\2=\"\\3\">";

		# email 형식이나 URL 에 포함될 경우 URL 보호를 위해 @ 을 치환
		$src[] = "/(http|https|ftp|telnet|news|mms):\/\/([^ \n@]+)@/i";
		$tar[] = "\\1://\\2_HTTPAT_\\3";

		# 특수 문자를 치환 및 html사용시 link 보호
		$src[] = "/&(quot|gt|lt)/i";
		$tar[] = "!\\1";
		$src[] = "/<a([^>]*)href=[\"' ]*({$regex['http']})[\"']*[^>]*>/i";
		$tar[] = "<A\\1HREF=\"\\3_orig://\\4\" TARGET=\"_blank\">";
		$src[] = "/href=[\"' ]*mailto:({$regex['mail']})[\"']*>/i";
		$tar[] = "HREF=\"mailto:\\2#-#\\3\">";
		$src[] = "/<([^>]*)(background|codebase|src)[ \n]*=[\n\"' ]*({$regex['http']})[\"']*/i";
		$tar[] = "<\\1\\2=\"\\4_orig://\\5\"";

		# 링크가 안된 url및 email address 자동링크
		$src[] = "/((SRC|HREF|BASE|GROUND)[ ]*=[ ]*|[^=]|^)({$regex['http']})/i";
		$tar[] = "\\1<A HREF=\"\\3\" TARGET=\"_blank\">\\3</a>";
		$src[] = "/({$regex['mail']})/i";
		$tar[] = "<A HREF=\"mailto:\\1\">\\1</a>";
		$src[] = "/<A HREF=[^>]+>(<A HREF=[^>]+>)/i";
		$tar[] = "\\1";
		$src[] = "/<\/A><\/A>/i";
		$tar[] = "</A>";

		# 보호를 위해 치환한 것들을 복구
		$src[] = "/!(quot|gt|lt)/i";
		$tar[] = "&\\1";
		$src[] = "/(http|https|ftp|telnet|news|mms)_orig/i";
		$tar[] = "\\1";
		$src[] = "'#-#'";
		$tar[] = "@";
		$src[] = "/{$regex['file']}/i";
		$tar[] = "\\1";

		# email 주소를 변형시킴
		$src[] = "/{$regex['mail']}/i";
		$tar[] = "\\1 at \\2";
		$src[] = "/<A HREF=\"mailto:([^ ]+) at ([^\">]+)/i";
		$tar[] = "<A HREF=\"act.php?o[at]=ma&amp;target=\\1{$rmail['chars']}\\2";

		# email 주소를 변형한 뒤 URL 속의 @ 을 복구
		$src[] = "/_HTTPAT_/";
		$tar[] = "@";

		# 이미지에 보더값 0 을 삽입
		$src[] = "/<(IMG SRC=\"[^\"]+\")>/i";
		$tar[] = "<\\1 BORDER=0>";

		# IE 가 아닌 경우 embed tag 를 삭제함
		if($agent['br'] != "MSIE") {
			$src[] = "/<embed/i";
			$tar[] = "&lt;embed";
		}

		$str = preg_replace($src,$tar,$str);
		return $str;
	}
}
?>
