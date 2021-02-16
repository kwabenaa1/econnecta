function sfGetParams(script_name) {
    // Find all script tags

    var scripts = document.getElementsByTagName("script");

    // Look through them trying to find ourselves

    for(var i=0; i<scripts.length; i++) {
        if(scripts[i].src.indexOf("/" + script_name) > -1) {
            // Get an array of key=value strings of params

            var pa = scripts[i].src.split("?").pop().split("&");

            // Split each key=value into array, the construct js object

            var p = {};
            for(var j=0; j<pa.length; j++) {
                var kv = pa[j].split("=");
                p[kv[0]] = kv[1];
            }
            return p;
        }
    }

    // No scripts match

    return {};
}
debugger;
var sfParams=sfGetParams('smart-forms-global-vars');
var smartFormsRootPath=sfParams.path;
// echo 'var smartFormsRootPath="'.$_GET['path'].'";';