/**
 * Helper functions to convert between different color formats.
 *
 * @author      Alexander Ebert
 * @copyright	2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @module	ColorUtil (alias)
 * @module      WoltLabSuite/Core/ColorUtil
 */
define([], function () {
	"use strict";
	
	/**
	 * @exports     WoltLabSuite/Core/ColorUtil
	 */
	var ColorUtil = {
		/**
		 * Converts a HSV color into RGB.
		 *
		 * @see	https://secure.wikimedia.org/wikipedia/de/wiki/HSV-Farbraum#Transformation_von_RGB_und_HSV
		 *
		 * @param       {int}           h
		 * @param       {int}           s
		 * @param       {int}           v
		 * @return      {Object}
		 */
		hsvToRgb: function(h, s, v) {
			var rgb = { r: 0, g: 0, b: 0 };
			var h2, f, p, q, t;
			
			h2 = Math.floor(h / 60);
			f = h / 60 - h2;
			
			s /= 100;
			v /= 100;
			
			p = v * (1 - s);
			q = v * (1 - s * f);
			t = v * (1 - s * (1 - f));
			
			if (s == 0) {
				rgb.r = rgb.g = rgb.b = v;
			}
			else {
				switch (h2) {
					case 1:
						rgb.r = q;
						rgb.g = v;
						rgb.b = p;
						break;
					
					case 2:
						rgb.r = p;
						rgb.g = v;
						rgb.b = t;
						break;
					
					case 3:
						rgb.r = p;
						rgb.g = q;
						rgb.b = v;
						break;
					
					case 4:
						rgb.r = t;
						rgb.g = p;
						rgb.b = v;
						break;
					
					case 5:
						rgb.r = v;
						rgb.g = p;
						rgb.b = q;
						break;
					
					case 0:
					case 6:
						rgb.r = v;
						rgb.g = t;
						rgb.b = p;
						break;
				}
			}
			
			return {
				r: Math.round(rgb.r * 255),
				g: Math.round(rgb.g * 255),
				b: Math.round(rgb.b * 255)
			};
		},
		
		/**
		 * Converts a RGB color into HSV.
		 *
		 * @see	https://secure.wikimedia.org/wikipedia/de/wiki/HSV-Farbraum#Transformation_von_RGB_und_HSV
		 *
		 * @param       {int}           r
		 * @param       {int}           g
		 * @param       {int}           b
		 * @return	{Object}
		 */
		rgbToHsv: function(r, g, b) {
			var h, s, v;
			var max, min, diff;
			
			r /= 255;
			g /= 255;
			b /= 255;
			
			max = Math.max(Math.max(r, g), b);
			min = Math.min(Math.min(r, g), b);
			diff = max - min;
			
			h = 0;
			if (max !== min) {
				switch (max) {
					case r:
						h = 60 * ((g - b) / diff);
						break;
					
					case g:
						h = 60 * (2 + (b - r) / diff);
						break;
					
					case b:
						h = 60 * (4 + (r - g) / diff);
						break;
				}
				
				if (h < 0) {
					h += 360;
				}
			}
			
			if (max === 0) {
				s = 0;
			}
			else {
				s = diff / max;
			}
			
			v = max;
			
			return {
				h: Math.round(h),
				s: Math.round(s * 100),
				v: Math.round(v * 100)
			};
		},
		
		/**
		 * Converts HEX into RGB.
		 *
		 * @param       {string}        hex
		 * @return      {Object}
		 */
		hexToRgb: function(hex) {
			if (/^#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(hex)) {
				// only convert #abc and #abcdef
				var parts = hex.split('');
				
				// drop the hashtag
				if (parts[0] === '#') {
					parts.shift();
				}
				
				// parse shorthand #xyz
				if (parts.length === 3) {
					return {
						r: parseInt(parts[0] + '' + parts[0], 16),
						g: parseInt(parts[1] + '' + parts[1], 16),
						b: parseInt(parts[2] + '' + parts[2], 16)
					};
				}
				else {
					return {
						r: parseInt(parts[0] + '' + parts[1], 16),
						g: parseInt(parts[2] + '' + parts[3], 16),
						b: parseInt(parts[4] + '' + parts[5], 16)
					};
				}
			}
			
			return Number.NaN;
		},
		
		/**
		 * Converts a RGB into HEX.
		 *
		 * @see	http://www.linuxtopia.org/online_books/javascript_guides/javascript_faq/rgbtohex.htm
		 *
		 * @param       {int}           r
		 * @param       {int}           g
		 * @param       {int}           b
		 * @return      {string}
		 */
		rgbToHex: function(r, g, b) {
			var charList = "0123456789ABCDEF";
			
			if (g === undefined) {
				if (r.toString().match(/^rgba?\((\d+), ?(\d+), ?(\d+)(?:, ?[0-9.]+)?\)$/)) {
					r = RegExp.$1;
					g = RegExp.$2;
					b = RegExp.$3;
				}
			}
			
			return (charList.charAt((r - r % 16) / 16) + '' + charList.charAt(r % 16)) + '' + (charList.charAt((g - g % 16) / 16) + '' + charList.charAt(g % 16)) + '' + (charList.charAt((b - b % 16) / 16) + '' + charList.charAt(b % 16));
		}
	};
	
	// WCF.ColorPicker compatibility (color format conversion)
	window.__wcf_bc_colorUtil = ColorUtil;
	
	return ColorUtil;
});
