function flowchart(a, element, name) {
	var $element = $("#" + element);
	function b() {
		var b, c = 0,
			d = [],
			e = [];
		for (b = a, len = b.length, size = b.length, c = size > 9 ? 9 : size - 1; c >= 0; c--) {
			var f = b[c];
			d.push(f.label),
			e.push(f.value)
		}
		var g = $element.highcharts();
		g.series[0].setData(e),
		g.xAxis[0].setCategories(d)
	}
	var c = 0;
	picDate = {
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: !1,
			type: "line",		// "column"
			renderTo: element,
			height: 290
		},
		title: {
			text: " "
		},
		navigation: {
			buttonOptions: {
				enabled: !1
			}
		},
		credits: {
			enabled: !1
		},
		tooltip: {
			headerFormat: '<span style="font-size:10px"></span><table>',
			pointFormat: '<tr"><td style="color:{series.color};padding:5px 10px">{series.name}</td><td style="padding:5px 10px;"><b style="font-weight:normal;letter-spacing:-1px;">{point.y}</b></td></tr>',
			footerFormat: "</table>",
			useHTML: !0,
			borderColor: "#c0d0e0",
			borderWidth: 1
		},
		plotOptions: {
			column: {
				pointPadding: .2,
				borderWidth: .9
			}
		},
		xAxis: {
			categories: []
		},
		yAxis: {
			title: {
				text: "",
				margin: 20,
				align: "middle"
			}
		},
		series: [
			{
				name: name,
				color: "#57af65",
				marker: {
					symbol: "circle"
				},
				data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
			}
		]
	},
	cylindricalDate = new Highcharts.Chart(picDate),
	b(),

	$element.siblings('.move_left').unbind(),
	$element.siblings('.move_right').unbind(),
	$element.siblings('.move_left').click(function () {
		if (!a)
			return !1;
		var b, d, e = [],
			f = [],
			g = a.length,
			h = Math.ceil(g / 10);
		if (10 > g)
			return !1;
		if (c >= h - 1 || c >= 3)
			return !1;
		c++,
		0 == c ? (b = 0, d = 9) : 
			1 == c && g > 10 ? (b = 10, d = g % 10 != 0 && 21 > g ? g % 10 + 9 : 19) : 
				2 == c && g > 20 ? (b = 20, d = g % 10 != 0 && 31 > g ? g % 10 + 19 : 29) : 
					(b = 30, d = 30);
		for (var i = d; i >= b; i--) {
			var j = a[i];
			e.push(j.label),
			f.push(j.value)
        }
		var k = $element.highcharts();
		k.series[0].setData(f),
		k.xAxis[0].setCategories(e)
    }),
	$element.siblings('.move_right').click(function () {
		if (!a)
			return !1; 
		var b, d, e = [],
			f = [],
			g = a.length;
		if (0 >= c)
			return !1;
		c--,
		0 == c ? (b = 0, d = 9) : 
			1 == c && g > 10 ? (b = 10, d = g % 10 != 0 && 21 > g ? g % 10 + 9 : 19) : 
				2 == c && g > 20 ? (b = 20, d = g % 10 != 0 && 31 > g ? g % 10 + 19 : 29) : 
					(b = 30, d = 30);
		for (var h = d; h >= b; h--) {
			var i = a[h];
			e.push(i.label),
			f.push(i.value)
		}
		var j = $element.highcharts();
		j.series[0].setData(f),
		j.xAxis[0].setCategories(e)
	})
}