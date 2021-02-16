<?php die("Access Denied"); ?>#x#var style = Object.assign({}, Assets),
          id = "#{{ field("id") }}",
          css = "";
// margin
style.margin(id, {{ field("margin") | json_encode() }})

// padding 
style.padding(id, {{ field("padding") | json_encode() }})

// Z-Index
style.css(id, 'z-index', {{ field("zindex") | json_encode() }})

// background 
style.background (id, {{ field("background") | json_encode() }})

// border + box shadow 
style.border (id, {{ field("border") | json_encode() }})

var rawCss = "{{ field("custom_css").code | default("") | removeLines }}";

// custom css 
style.raw (rawCss)
