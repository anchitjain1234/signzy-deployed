$(function() {
	$("#tags").autocomplete({
		source: "http://localhost/cakephp/col/index.json"
	});
});
