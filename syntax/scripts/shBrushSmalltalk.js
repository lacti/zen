;(function()
{
	// CommonJS
	typeof(require) != 'undefined' ? SyntaxHighlighter = require('shCore').SyntaxHighlighter : null;

	function Brush()
	{
		var keywords = 'self super';

		this.regexList = [
			{ regex: SyntaxHighlighter.regexLib.multiLineDoubleQuotedString,	css: 'comments'},
			{ regex: SyntaxHighlighter.regexLib.singleQuotedString,				css: 'color2'},
			{ regex: /\b([\d]+(\.[\d]+)?|0x[a-f0-9]+)\b/gi,						css: 'color2'},		// numbers
			{ regex: /\b([A-Z])[\d\w]*/g,										css: 'constants'},	// class names
			{ regex: new RegExp(this.getKeywords(keywords), 'gmi'),				css: 'keyword'}
		];
	
		this.forHtmlScript({
			left	: /(&lt;|<)%[@!=]?/g, 
			right	: /%(&gt;|>)/g 
		});
	}

	Brush.prototype	= new SyntaxHighlighter.Highlighter();
	Brush.aliases	= ['st', 'smalltalk'];

	SyntaxHighlighter.brushes.Smalltalk = Brush;

	// CommonJS
	typeof(exports) != 'undefined' ? exports.Brush = Brush : null;
})();
