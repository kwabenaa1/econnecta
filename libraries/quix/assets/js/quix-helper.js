// ----------------------------------------------------------------------------
// Assets Helper, a Javascript Assets Helper library Licensed under the MIT
// license.
// ----------------------------------------------------------------------------
// Copyright (C) Iftekher Sunny < iftekhersunny@hotmail.com >
// ----------------------------------------------------------------------------
(function ($, _) {

  /////////////////////////////////////////////////////////
  //
  // instance of global object
  //
  /////////////////////////////////////////////////////////
  var root = window;

  /////////////////////////////////////////////////////////
  //
  // instance of assets object
  //
  /////////////////////////////////////////////////////////
  var Assets = root.Assets || {
    name: "assets-helper",
    version: "0.0.0",
    compiledCss: '',
    fontFamilies: []
  };

  /////////////////////////////////////////////////////////
  //
  // instance of assets._css object
  //
  /////////////////////////////////////////////////////////
  Assets._css = {
    desktop: {},
    tablet: {},
    phone: {},
    raw: ''
  };

  /////////////////////////////////////////////////////////
  //
  // Load Global Styles
  //
  /////////////////////////////////////////////////////////
  root.loadGlobalStyles = function (globalStyles) {
    if (!globalStyles) return;

    let globalStylesAsset = Object.create(Assets);

    globalStyles.forEach(function (style) {
      let selector = style.get
        ? style.get('selector') == 'custom_selector' ? style.get('custom_selector') : style.get('selector')
        : style.selector == 'custom_selector' ? style.custom_selector : style.selector;

      let rule = style.get
        ? style.get('rule').toJS ? style.get('rule').toJS() : style.get('rule')
        : style.rule;

      let ruleType = style.get
        ? style.get('rule_type')
        : style.rule_type

      switch (ruleType) {
        case 'typography':
          globalStylesAsset.typography(selector, rule)
          break;
        case 'link':
          globalStylesAsset.typography(selector, rule.normal)
          globalStylesAsset.typography(selector + ":hover", rule.hover)
          break;
        case 'custom_css':
          globalStylesAsset.raw(`${selector} { ${rule} }`)
          break;
      }
    })

    globalStylesAsset.load("global-styles");
  }

  Assets.storeCompiledCss = function (file, token) {
    // document.addEventListener("DOMContentLoaded", function() {
    //   setTimeout(function() {
    //     const token = jQuery("#jform_token").attr('name');
    //     const tokenValue = jQuery("#jform_token").val();
    //     const url = 'index.php?option=com_quix&task=api.storeCompiledCSs&type=element&details=true&' + token + '=' + tokenValue;
    //     console.log("Assets.compiledCss", Assets.compiledCss)
    //     jQuery.ajax({
    //       url: url,
    //       type: "post",
    //       data: { 'compiled_css': Assets.compiledCss, 'compiled_css_file_name': file }
    //     })
    //   }, 300);
    // });

    // const token = jQuery("#jform_token").attr('name');
    // const tokenValue = jQuery("#jform_token").val();

    const url = QUIX_ROOT_URL + 'index.php?option=com_quix&task=api.storeCompiledCSs&type=element&details=true&' + token + '=1';
    // console.log(Assets.fontFamilies);
    jQuery.ajax({
      url: url,
      type: "post",
      data: { 'compiled_css': Assets.compiledCss, 'compiled_css_file_name': file, 'font_families': Assets.fontFamilies }
    })

    Assets.compiledCss = '';
  }

  /////////////////////////////////////////////////////////
  //
  // storing raw css rules
  //
  /////////////////////////////////////////////////////////
  Assets.raw = function (rules) {
    if (rules) {
      if (!Assets._css.raw) Assets._css.raw = '';

      Assets._css.raw += rules;
    }
  }

  /////////////////////////////////////////////////////////
  //
  // storing responsive css rules
  //
  /////////////////////////////////////////////////////////
  Assets._responsiveCssRules = function (device, selector, rules) {
    if (!Assets._css[device][selector]) {
      Assets._css[device][selector] = '';
    }

    Assets._css[device][selector] += rules;
  }

  /////////////////////////////////////////////////////////
  //
  // storing desktop css rules
  //
  /////////////////////////////////////////////////////////
  Assets.desktop = function (selector, rules) {
    Assets._responsiveCssRules("desktop", selector, rules);
  }

  /////////////////////////////////////////////////////////
  //
  // storing tablet css rules
  //
  /////////////////////////////////////////////////////////
  Assets.tablet = function (selector, rules) {
    Assets._responsiveCssRules("tablet", selector, rules);
  }

  /////////////////////////////////////////////////////////
  //
  // storing phone css rules
  //
  /////////////////////////////////////////////////////////
  Assets.phone = function (selector, rules) {
    Assets._responsiveCssRules("phone", selector, rules);
  }


  //=============================================
  // CSS API's to use from element/node
  //=============================================

  /////////////////////////////////////////////////////////
  // Wrapper function for Assets.desktop
  // Provide clean and understanable API for dev
  /////////////////////////////////////////////////////////
  Assets.css = function (selector, prop, value) {
    if (_.isObject(value)) {
      if (!value.value) return;

      Assets.desktop(selector, Assets._prop(prop, value.value));
    } else {
      Assets.desktop(selector, Assets._prop(prop, value));
    }
  }

  // Margin (Responsive)
  //=============================================
  Assets.margin = function (selector, field) {
    Assets.desktop(selector, Assets._cssForDimensions(field, field.unit, 'margin'));

    if (field.responsive) {
      Assets.tablet(selector, Assets._cssForDimensions(field.tablet, field.unit, 'margin'));
      Assets.phone(selector, Assets._cssForDimensions(field.phone, field.unit, 'margin'));
    }
  }

  // Padding (responsive)
  //=============================================
  Assets.padding = function (selector, field) {
    Assets.desktop(selector, Assets._cssForDimensions(field, field.unit, 'padding'));

    if (field.responsive) {
      Assets.tablet(selector, Assets._cssForDimensions(field.tablet, field.unit, 'padding'));
      Assets.phone(selector, Assets._cssForDimensions(field.phone, field.unit, 'padding'));
    }
  }


  // Width (responsive)
  //=============================================
  Assets.width = function (selector, field) {
    const unit = field.unit || "px";
    field = field.value || field;

    return Assets.responsiveCss(selector, field, 'width', unit);
  }

  // Height (responsive)
  //=============================================
  Assets.height = function (selector, field) {
    const unit = field.unit || "px";
    field = field.value || field;

    return Assets.responsiveCss(selector, field, 'height', unit);
  }

  // Min Height (responsive)
  //=============================================
  Assets.minHeight = function (selector, field) {
    const unit = field.unit || "px";
    field = field.value || field;

    return Assets.responsiveCss(selector, field, 'min-height', unit);
  }

  // Typography
  //=============================================
  Assets.typography = function (selector, field) {
    // Family
    if (!_.isEmpty(field.family)) {
      
      // fallback font-weight
      field.weight = !_.isEmpty(field.weight) ? field.weight : 400
      Assets.css(selector, 'font-family', field.family)

      // new font-weight system
      // TODO: legacy
      var loadGoogleFontAgain = false;
      
      var hasfontFamilies = Assets.fontFamilies.find(e => { return e.family === field.family ? e : false});
      if(hasfontFamilies){
        if(hasfontFamilies.weight.indexOf(field.weight) == -1)
        {
          // not found;
          loadGoogleFontAgain = true;
          hasfontFamilies.weight.push(field.weight);
        }
      }else{
        // not found;
        loadGoogleFontAgain = true;
        Assets.fontFamilies.push({ 'family' : field.family, 'weight' : [field.weight] });
      }
      
      if(loadGoogleFontAgain){
        // dynamically google fonts loading.
        let fontNotLoaded = !jQuery('link[href="http://fonts.googleapis.com/css?family=' + field.family.replace(" ", "+") + '"]').length;
  
        if (fontNotLoaded) {
          try {
            WebFont.load({
              google: {
                families: [`${field.family}:${field.weight}`]
              }
            });
          } catch (e) {
            console.log('WebFont not loaded!');
          }
        }
      } // end of loadGoogleFontAgain      

    }
    // weight
    if (!_.isEmpty(field.weight)) {
      Assets.fontWeight(selector, field.weight)
    }
    // Size
    if (!_.isEmpty(field.size)) {
      Assets.fontSize(selector, field.size)
    }
    // Transform
    if (!_.isEmpty(field.transform)) {
      Assets.css(selector, 'text-transform', field.transform)
    }
    // Style
    if (!_.isEmpty(field.style)) {
      Assets.css(selector, 'font-style', field.style)
    }
    // Decoration
    if (!_.isEmpty(field.decoration)) {
      Assets.css(selector, 'text-decoration', field.decoration)
    }
    // Line Height
    if (!_.isEmpty(field.height)) {
      Assets.lineHeight(selector, field.height)
    }
    // Letter spacing
    if (!_.isEmpty(field.spacing)) {
      Assets.letterSpacing(selector, field.spacing)
    }

    if (field.text_shadow) {
      if (!_.isEmpty(field.text_shadow.color)) {
        Assets.css(selector, 'text-shadow', field.text_shadow.color + ' ' +
          field.text_shadow.horizontal.value + field.text_shadow.horizontal.unit + ' ' +
          field.text_shadow.vertical.value + field.text_shadow.vertical.unit + ' ' +
          field.text_shadow.blur.value + field.text_shadow.blur.unit)
      }
    }
  }

  // Font-weight
  //=============================================
  Assets.fontWeight = function (selector, weight) {
    var fontStyle = false

    switch (weight) {
      case '100':
        weight = 100;
        break;
      case '200':
        weight = 200;
        break;
      case '300':
        weight = 300;
        break;
      case '400':
        weight = 400;
        break;
      case '500':
        weight = 500;
        break;
      case '600':
        weight = 600;
        break;
      case '700':
        weight = 700;
        break;
      case '800':
        weight = 800;
      case '900':
        weight = 900;
        break;
      case 'regular':
        weight = 400;
        break;
      case '100italic':
        weight = 100;
        fontStyle = true;
        break;
      case '300italic':
        weight = 300;
        fontStyle = true;
        break;
      case '500italic':
        weight = 500;
        fontStyle = true;
        break;
      case '600italic':
        weight = 600;
        fontStyle = true;
        break;
      case '700italic':
        weight = 700;
        fontStyle = true;
        break;
      case '800italic':
        weight = 800;
        fontStyle = true;
        break;
      case '900italic':
        weight = 900;
        fontStyle = true;
        break;
    }

    Assets.css(selector, 'font-weight', weight);

    if (fontStyle) {
      Assets.css(selector, 'font-style', 'italic');
    }
  }

  // Font size (responsive)
  //=============================================
  Assets.fontSize = function (selector, field) {
    const unit = field.unit || "px";
    field = field.value || field;

    Assets.responsiveCss(selector, field, 'font-size', unit)
  }

  // line-height (responsive)
  //=============================================
  Assets.lineHeight = function (selector, field) {
    const unit = field.unit || "em";
    field = field.value || field;
    Assets.responsiveCss(selector, field, 'line-height', unit)
  }

  // Letter spacing (responsive)
  //=============================================
  Assets.letterSpacing = function (selector, field) {
    const unit = field.unit || "px";
    field = field.value || field;
    Assets.responsiveCss(selector, field, 'letter-spacing', unit)
  }

  // Text alignement (responsive)
  Assets.alignment = function (selector, field) {
    if (_.isEmpty(field)) return;

    if (!_.isEmpty(field.desktop.value)) {
      Assets.desktop(selector, Assets._prop('text-align', field.desktop.value))
    }

    if (field.responsive) {
      if (!_.isEmpty(field.tablet.value)) {
        Assets.tablet(selector, Assets._prop('text-align', field.tablet.value))
      }
      if (!_.isEmpty(field.phone.value)) {
        Assets.phone(selector, Assets._prop('text-align', field.phone.value))
      }
    }
  }

  // float (responsive)
  //=============================================
  Assets.float = function (selector, field) {

    if ((field.desktop == 'left') || (field.desktop == 'right')) {
      Assets.desktop(selector, Assets._prop('float', field.desktop))
    }

    if (field.responsive) {
      if ((field.tablet == 'left') || (field.tablet == 'right')) {
        Assets.tablet(selector, Assets._prop('float', field.tablet))
      }

      if ((field.phone == 'left') || (field.phone == 'right')) {
        Assets.phone(selector, Assets._prop('float', field.phone))
      }
    }
  }

  // Background
  //=============================================
  Assets.background = function (selector, field, hover_selector) {
    // Get state
    var state = field.state;

    if (!state) return;

    // State : Normal
    // ---------------------------------------
    var normal = state.normal

    // Type = Gradient
    if (normal.type == 'gradient') {
      var color_1 = normal.properties.color_1
      var color_2 = normal.properties.color_2
      // Gradient Type

      var gradient_type = normal.properties.type
      var direction = normal.properties.direction.value ? normal.properties.direction.value : normal.properties.direction;

      // Suffix position with %
      var start_position = normal.properties.start_position.value ? normal.properties.start_position.value : normal.properties.start_position;
      start_position += '%'
      var end_position = normal.properties.end_position.value ? normal.properties.end_position.value : normal.properties.end_position;
      end_position += '%';

      var css = color_1 + ' ' + start_position + ',' + color_2 + ' ' + end_position

      if (gradient_type == 'linear') {
        direction = direction + 'deg'
        css = direction.value ? direction.value : direction + ', ' + css

        Assets.css(selector, 'background', 'linear-gradient(' + css + ')')
      }
      if (gradient_type == 'radial') {
        direction = 'at ' + direction
        css = direction + ', ' + css

        Assets.css(selector, 'background', 'radial-gradient(' + css + ')')
      }

    }
    // Type : Classic
    if (normal.type == 'classic') {

      if (!_.isEmpty(normal.properties.color)) {
        Assets.css(selector, "background-color", normal.properties.color)
      }

      if (!_.isEmpty(normal.properties.src)) {

        let src = normal.properties.src;

        if (!_.isNull(src.url)) {
          Assets.css(selector, 'background-image', 'url("' + src.url + '")')
        } else {
          if (!_.isNull(src.media) && !_.isNull(src.media.type)) {
            if (src.media.type != "svg")
              Assets.css(selector, 'background-image', 'url("' + FILE_MANAGER_ROOT_URL + src.media.image + '")')
          }
        }

        Assets.css(selector, 'background-size', normal.properties.size)
        Assets.css(selector, 'background-position', normal.properties.position)
        Assets.css(selector, 'background-repeat', normal.properties.repeat)

        if (normal.properties.parallax && (normal.properties.parallax_method === 'css')) {
          Assets.css(selector, 'background-attachment', 'fixed')
        }
        if (normal.properties.blend != 'normal') {
          Assets.css(selector, 'background-blend-mode', normal.properties.blend)
        }
      }
    }
    // Opacity
    if (normal.required_opacity) {
      Assets.css(selector, "opacity", normal.opacity)
    }


    // State : Hover
    // -----------------------------------
    var hover = state.hover
    // Only used on transion effect because selector var is replced with :hover state
    var notHoverSelector = selector

    // Sometime we need to pass hover selector 
    // Like background overlay
    if (hover_selector) {
      selector = hover_selector + ':hover ' + selector
    } else {
      selector = selector + ':hover'
    }

    // Type = Gradient
    if (hover.type == 'gradient') {
      var color_1 = hover.properties.color_1
      var color_2 = hover.properties.color_2

      // Gradient Type
      var gradient_type = hover.properties.type
      var direction = hover.properties.direction.value ? hover.properties.direction.value : hover.properties.direction;

      // Suffix position with %
      var start_position = hover.properties.start_position.value ? hover.properties.start_position.value : hover.properties.start_position;
      start_position += '%'
      var end_position = hover.properties.end_position.value ? hover.properties.end_position.value : hover.properties.end_position;
      end_position += '%';

      var css = color_1 + ' ' + start_position + ',' + color_2 + ' ' + end_position

      if (gradient_type == 'linear') {
        direction = direction + 'deg'
        css = direction.value ? direction.value : direction + ', ' + css

        Assets.css(selector, 'background', 'linear-gradient(' + css + ')')
      }

      if (gradient_type == 'radial') {
        direction = 'at ' + direction
        css = direction + ', ' + css

        Assets.css(selector, 'background', 'radial-gradient(' + css + ')')
      }
      // Transition
      if (hover.required_transition) {
        var transitionValue = hover.transition.value ? hover.transition.value : hover.transition;
        var transition = "background " + transitionValue + "s, opacity " + transitionValue + "s ease-in"
        transition = transition + ", border " + transitionValue + "s ease-in, box-shadow " + transitionValue + "s ease-in"
        Assets.css(notHoverSelector, "transition", transition)
      }
    }
    // Type : Image.
    if (hover.type == 'classic') {
      if (!_.isEmpty(hover.properties.color)) {
        Assets.css(selector, "background-color", hover.properties.color)
      }
      if (!_.isEmpty(hover.properties.src)) {

        let src = hover.properties.src;

        if (!_.isNull(src.url)) {
          Assets.css(selector, 'background-image', 'url("' + src.url + '")')
        } else {
          if (!_.isNull(src.media) && !_.isNull(src.media.type)) {
            if (src.media.type != "svg")
              Assets.css(selector, 'background-image', 'url("' + FILE_MANAGER_ROOT_URL + src.media.image + '")')
          }
        }

        Assets.css(selector, 'background-size', hover.properties.size)
        Assets.css(selector, 'background-position', hover.properties.position)
        Assets.css(selector, 'background-repeat', hover.properties.repeat)

        if (hover.properties.blend != 'normal') {
          Assets.css(selector, 'background-blend-mode', hover.properties.blend)
        }
      }

      // Transition
      if (hover.required_transition && (!_.isEmpty(hover.properties.color) || !_.isEmpty(hover.properties.src))) {
        var transitionValue = hover.transition.value ? hover.transition.value : hover.transition;
        var transition = "background " + transitionValue + "s, opacity " + transitionValue + "s ease-in"
        transition = transition + ", border " + transitionValue + "s ease-in, box-shadow " + transitionValue + "s ease-in"
        Assets.css(notHoverSelector, "transition", transition)
      }
    }
    // Opacity
    if (hover.required_opacity) {
      Assets.css(selector, "opacity", hover.opacity)
    }
  }

  // Border
  //=============================================
  Assets.border = function (selector, field, hover_selector) {
    // Get state
    var state = field.state;

    if (!state) return;

    // State : Normal
    // ---------------------------------------
    var normal = state.normal

    // Border
    if (normal.properties.border_type !== 'none') {
      // style
      Assets.css(selector, 'border-style', normal.properties.border_type)
      // color
      Assets.css(selector, 'border-color', normal.properties.border_color)
      // width
      Assets.desktop(selector, Assets._cssForBorderWidth(normal.properties.border_width, normal.properties.border_width.unit))
      // radius
      Assets.desktop(selector, Assets._cssForBorderRadius(normal.properties.border_radius, normal.properties.border_radius.unit))
    }

    // Box Shadow
    // ---------------------------------------
    var shadow = normal.properties.box_shadow;

    if (shadow.color) {
      var boxShadow = ((shadow.position == 'inset') ? 'inset ' : '') +
        shadow.horizontal.value + shadow.horizontal.unit + ' ' +
        shadow.vertical.value + shadow.vertical.unit + ' ' +
        shadow.blur.value + shadow.blur.unit + ' ' +
        shadow.spread.value + shadow.spread.unit + ' ' +
        shadow.color

      Assets.css(selector, "box-shadow", boxShadow)
    }

    // State : Hover
    // ---------------------------------------
    var hover = state.hover
    if (hover.properties.border_type !== 'none') {
      // style
      Assets.css(selector + ':hover', 'border-style', hover.properties.border_type)
      // color
      Assets.css(selector + ':hover', 'border-color', hover.properties.border_color)
      // width
      Assets.desktop(selector + ':hover', Assets._cssForBorderWidth(hover.properties.border_width, hover.properties.border_width.unit))
      // radius
      Assets.desktop(selector + ':hover', Assets._cssForBorderRadius(hover.properties.border_radius, hover.properties.border_radius.unit))
    }
    // Box Shadow
    // ---------------------------------------
    var shadow_hover = hover.properties.box_shadow;

    if (shadow_hover.color) {
      var boxShadow_hover = ((shadow_hover.position == 'inset') ? 'inset ' : '') +
        shadow_hover.horizontal.value + shadow_hover.horizontal.unit + ' ' +
        shadow_hover.vertical.value + shadow_hover.vertical.unit + ' ' +
        shadow_hover.blur.value + shadow_hover.blur.unit + ' ' +
        shadow_hover.spread.value + shadow_hover.spread.unit + ' ' +
        shadow_hover.color

      Assets.css(selector + ':hover', "box-shadow", boxShadow_hover)
    }

    // Transition
    var transitionValue = hover.properties.transition.value ? hover.properties.transition.value : hover.properties.transition;
    var transition = "border " + transitionValue + "s ease-in, box-shadow " + transitionValue + "s ease-in"
    transition = transition + ",background " + transitionValue + "s, opacity " + transitionValue + "s ease-in"
    Assets.css(selector, "transition", transition)
  }
  // Border Width
  //=============================================
  Assets.borderWidth = function (selector, field) {
    Assets.desktop(selector, Assets._cssForBorderWidth(field));

    if (field.responsive) {
      Assets.tablet(selector, Assets._cssForBorderWidth(field.tablet));
      Assets.phone(selector, Assets._cssForBorderWidth(field.phone));
    }
  }

  // Border Width
  //=============================================
  Assets.borderRadius = function (selector, field) {
    const unit = field.unit || "px";
    Assets.desktop(selector, Assets._cssForBorderRadius(field, unit));

    if (field.responsive) {
      Assets.tablet(selector, Assets._cssForBorderRadius(field.tablet, unit));
      Assets.phone(selector, Assets._cssForBorderRadius(field.phone, unit));
    }
  }

  //=============================================
  // Private functions
  //=============================================

  // Generate CSS for border width
  //=============================================
  Assets._cssForBorderWidth = function (field, unit) {
    var css = '',
      unit = (unit) ? unit : 'px';

    css += Assets._prop('border-top-width', field.top + unit);
    css += Assets._prop('border-right-width', field.right + unit);
    css += Assets._prop('border-bottom-width', field.bottom + unit);
    css += Assets._prop('border-left-width', field.left + unit);
    return css;
  }

  // Generate CSS for border radius
  //=============================================
  Assets._cssForBorderRadius = function (field, unit) {
    var css = '',
      unit = (unit) ? unit : 'px';

    css += field.top ? Assets._prop('border-top-left-radius', field.top + unit) : ''
    css += field.right ? Assets._prop('border-top-right-radius', field.right + unit) : ''
    css += field.bottom ? Assets._prop('border-bottom-right-radius', field.bottom + unit) : ''
    css += field.left ? Assets._prop('border-bottom-left-radius', field.left + unit) : ''
    return css;
  }

  // Generate CSS for dimensions
  //=============================================
  Assets._cssForDimensions = function (field, unit, type) {
    var css = '',
      unit = (!_.isEmpty(unit)) ? unit : 'px';

    css += Assets._prop(type + '-top', field.top + unit);
    css += Assets._prop(type + '-right', field.right + unit);
    css += Assets._prop(type + '-bottom', field.bottom + unit);
    css += Assets._prop(type + '-left', field.left + unit);

    return css;
  }

  // Create css prop : value rules
  //=============================================
  Assets._prop = function (prop, value, $boolean) {
    $boolean = $boolean || null;

    var zeroAllowedProp = [
      'padding-bottom',
      'padding-top',
      'padding-right',
      'padding-left',
      'margin-bottom',
      'margin-top',
      'margin-right',
      'margin-left',
      'border-width',
      'border-top-width',
      'border-bottom-width',
      'border-right-width',
      'border-left-width'
    ];

    if (
      !value ||
      (0 == value) ||
      (0 < value) ||
      ('0%' == value) ||
      ('0px' == value) ||
      ('0em' == value) ||
      ('0rem' == value) ||
      ('0vh' == value) ||
      _.isEmpty(value)
    ) {

      if (_.isNumber(value) && 0 != value) return prop + " : " + value + "; ";
      else if (zeroAllowedProp.indexOf(prop) != -1) return prop + " : " + value + "; ";
      else return "";
    }

    return value == "px" || value == "rem" || value == "em" || value == "vh" || value == "%"
      ? ""
      : prop + " : " + value + "; ";
  }

  // Create responsive CSS
  //=============================================
  Assets.responsiveCss = function (selector, field, prop, unit) {
    unit = unit || "";

    if (!_.isEmpty(field.desktop) || _.isNumber(field.desktop)) {
      Assets.desktop(selector, Assets._prop(prop, field.desktop + unit))
    }

    if (field.responsive) {
      if (!_.isEmpty(field.tablet) || _.isNumber(field.desktop)) {
        Assets.tablet(selector, Assets._prop(prop, field.tablet + unit))
      }
      if (!_.isEmpty(field.phone) || _.isNumber(field.desktop)) {
        Assets.phone(selector, Assets._prop(prop, field.phone + unit))
      }
    }
  }

  /////////////////////////////////////////////////////////
  //
  // appending stylesheet to the head tag
  //
  /////////////////////////////////////////////////////////
  Assets._appendStyleSheet = function (css, id, mountID) {
    var head = document.head || document.getElementsByTagName('head')[0],
      style = document.createElement('style');

    style.type = 'text/css';
    if (style.styleSheet) {
      style.styleSheet.cssText = css;
    } else {
      style.appendChild(document.createTextNode(css));
    }

    if (mountID) {
      id = mountID.replace("#", "");
      id = id + '-preview';
    } else {
      id = id.replace("#", "");
      id = id + '-preview';
    }
    style.id = id;
    
    // if style already exists then remove it
    if(document.querySelector("style#" + id)) document.querySelector("style#" + id).remove();

    // append style
    head.appendChild(style);
  }

  /////////////////////////////////////////////////////////
  //
  // Get Query Variable
  //
  /////////////////////////////////////////////////////////
  Assets.getQueryVariable = function (variable) {
    var query = window.location.search.substring(1);
    var vars = query.split('&');
    for (var i = 0; i < vars.length; i++) {
      var pair = vars[i].split('=');
      if (decodeURIComponent(pair[0]) == variable) {
        return decodeURIComponent(pair[1]);
      }
    }

    console.log('Query variable %s not found', variable);
  }

  /////////////////////////////////////////////////////////
  //
  // loading css rules
  //
  /////////////////////////////////////////////////////////
  Assets.load = function (id, mountID) {
    var desktop = '',
      tablet = "@media (min-width: 768px) and (max-width: 1024px) { ",
      phone = "@media (max-width: 767px) { ";

    // appending all desktop rules
    for (var key in Assets._css.desktop) {
      desktop += key + " { " + Assets._css.desktop[key] + " } ";
    }

    // appending all tablet rules
    for (var key in Assets._css.tablet) {
      tablet += key + " { " + Assets._css.tablet[key] + " } ";
    }

    tablet += " } ";

    // appending all phone rules
    for (var key in Assets._css.phone) {
      phone += key + " { " + Assets._css.phone[key] + " } ";
    }

    phone += " } ";

    let raw = Assets._css.raw ? Assets._css.raw : '';

    // appending responsive rules ( desktop, tablet, and phone ) to the style tag
    Assets._appendStyleSheet(desktop + tablet + phone + raw, id, mountID);

    // if (Assets.getQueryVariable('optimize')) {
    //   Assets.compiledCss += desktop + tablet + phone + raw; 
    // }

    Assets.compiledCss += desktop + tablet + phone + raw;

    Assets._css = {
      desktop: {},
      tablet: {},
      phone: {}
    };
  }

  // Assets.storeCompiledCss();

  /////////////////////////////////////////////////////////
  //
  // assigning Assets object to the Global object
  //
  /////////////////////////////////////////////////////////
  return root.Assets = Assets;

}(

  /////////////////////////////////////////////////////////
  //
  // determine jQuery existence
  //
  /////////////////////////////////////////////////////////
  window.jQuery ? window.jQuery : undefined,

  /////////////////////////////////////////////////////////
  //
  // determine lodash existence
  //
  /////////////////////////////////////////////////////////
  window._ ? window._ : undefined)
);
// ----------------------------------------------------------------------------
// Assets Helper, a Javascript Assets Helper library
// Licensed under the MIT license.
// ----------------------------------------------------------------------------
// Copyright (C) Iftekher Sunny < iftekhersunny@hotmail.com >
// ----------------------------------------------------------------------------
(function (_) {


    /////////////////////////////////////////////////////////
    //
    // instance of global object
    //
    /////////////////////////////////////////////////////////
    var root = window;


    
    /////////////////////////////////////////////////////////
    //
    // instance of assets object
    //
    /////////////////////////////////////////////////////////
    var Assets = root.Assets || {
        name: "assets-helper",
        version: "0.0.0"
    };



    /////////////////////////////////////////////////////////
    //
    // getting image url
    //
    /////////////////////////////////////////////////////////
    Assets.image = function(path) {
        var hostname = root.location.hostname,
            port = root.location.port == 80 ? "" : ":" + root.location.port,
            protocol = root.location.protocol,
            protocols = ['http', 'https', '//'];

        
        for(key in protocols) {
            if (path.indexOf(protocols[key]) != -1) return path;
        }

       return protocol + "//" + hostname + port + "/" + path;   
    }



    /////////////////////////////////////////////////////////
    //
    // assigning Assets object to the Global object
    //
    /////////////////////////////////////////////////////////
    return root.Assets = Assets;

}(
    /////////////////////////////////////////////////////////
    //
    // determine lodash existence
    //
    /////////////////////////////////////////////////////////
    window._ ? window._ : undefined)
);
// ----------------------------------------------------------------------------
// Assets Helper, a Javascript Assets Helper library
// Licensed under the MIT license.
// ----------------------------------------------------------------------------
// Copyright (C) Iftekher Sunny < iftekhersunny@hotmail.com >
// ----------------------------------------------------------------------------
if (window.twig) {
    (function ($, _, twig) {

        /////////////////////////////////////////////////////////
        //
        // instance of global object
        //
        /////////////////////////////////////////////////////////
        var root = window;



        /////////////////////////////////////////////////////////
        //
        // instance of assets object
        //
        /////////////////////////////////////////////////////////
        var Assets = root.Assets || {
            name: "assets-helper",
            version: "0.0.0"
        };



        /////////////////////////////////////////////////////////
        //
        // an array of loaded element
        //
        /////////////////////////////////////////////////////////
        Assets.loadedElement = [];



        /////////////////////////////////////////////////////////
        //
        // init
        //
        /////////////////////////////////////////////////////////
        Assets.init = function () {
            document.createElement("QuixTemplate");
            document.createElement("QuixHtml");
            document.createElement("QuixStyle");
            document.createElement("QuixScript");

            $(function () {
                $("QuixTemplate").css({ display: "none" })
            });

            // cheat way to ignore include "global.twig" when twigjs compile
            // twig({
            //     id: 'global.twig',
            //     data: ""
            // });

            let token = jQuery("#jform_token").attr('name');
            axios.get(`index.php?option=com_quix&task=api.getFileContent&file=animation&${token}=1`).then(function(response) {
                twig({
                    id: 'animation.twig',
                    data: response.data
                });

                axios.get(`index.php?option=com_quix&task=api.getFileContent&file=global&${token}=1`).then(function (response) {
                    // console.log("global", response)

                    twig({
                        id: 'global.twig',
                        data: response.data
                    });

                    if (typeof window.parent.window.loadQuixComponentBuilder == "function") window.parent.window.loadQuixComponentBuilder();
                })

                //if (typeof window.parent.window.loadQuixComponentBuilder == "function") window.parent.window.loadQuixComponentBuilder();
            });


        }

        /////////////////////////////////////////////////////////
        //
        // Helper Function: string replace all
        //
        /////////////////////////////////////////////////////////
        Assets._replaceAll = function(str, find, replace) {
            return str.replace(new RegExp(find, 'g'), replace);
        }


        /////////////////////////////////////////////////////////
        //
        // get html markup by the given template and data
        //
        /////////////////////////////////////////////////////////
        Assets.html = function (templateID, data, htmlRenderId) {
            data = data || {};

            // check QuixHtml tag existence
            if (!$("QuixTemplate" + templateID + " QuixHtml").html()) return;

            return twig({
                allowInlineIncludes: true,
                data: $("QuixTemplate" + templateID + " QuixHtml").html()
            })
                .renderAsync(Object.assign(data, { $: $, _: _, Assets, FILE_MANAGER_ROOT_URL }))
                .then(function (output) {
                    // console.log(
                    //     `======= DEDUG ${templateID} HTML =======`,
                    //     output,
                    //     `========================================`
                    // )
                    // output = output.replace("&lt;", "<").replace("&gt;", ">");
                    output = Assets._replaceAll(output, "&lt;", "<");
                    output = Assets._replaceAll(output, "&gt;", ">");

                    if (!htmlRenderId) return output;

                    $(htmlRenderId).html(output);
                })
        }



        /////////////////////////////////////////////////////////
        //
        // get css rules by the given template and data
        //
        /////////////////////////////////////////////////////////
        Assets.style = function (templateID, data, mountID) {
            data = data || {};

            // check QuixStyle tag existence
            if (!$("QuixTemplate" + templateID + " QuixStyle").html()) return;

            // making inline script content
            var script = twig({
                allowInlineIncludes: true,
                data: $("QuixTemplate" + templateID + " QuixStyle").html()
            })
                .render(Object.assign(data, { $: $, _: _, Assets, FILE_MANAGER_ROOT_URL }));

            // loading inline script
            var inlineScript = document.createElement("script");

            inlineScript.innerHTML = script;

            // if script already exists
            // then remove it
            if (mountID) {
                var id = mountID.replace("#", "");
                inlineScript.id = id;
            } else {
                var id = templateID.replace("#", "");
                inlineScript.id = id;
            }

            if(document.querySelector("script#" + id)) document.querySelector("script#" + id).remove();

            //console.log("sytle" , inlineScript)

            document.body.appendChild(inlineScript);

            Assets.load(templateID, mountID);
        }



        /////////////////////////////////////////////////////////
        //
        // loading script by the given template and data
        //
        /////////////////////////////////////////////////////////
        Assets.script = function (templateID, data, mountID) {
            data = data || {};

            // check QuixScript tag existence
            if (!$("QuixTemplate" + templateID + " QuixScript").html()) return;

            // making inline script content
            var script = twig({
                allowInlineIncludes: true,
                data: $("QuixTemplate" + templateID + " QuixScript").html()
            })
                .render(Object.assign(data, { $: $, _: _, Assets, FILE_MANAGER_ROOT_URL }));

            // getting dependencies
            var filters = $("QuixTemplate" + templateID + " QuixScript").attr('dependencies')
                ? $("QuixTemplate" + templateID + " QuixScript").attr('dependencies').split(",")
                : [];

            // loading all dependencies of the inline script
            if ($.inArray(templateID, Assets.loadedElement) == -1) {
                for (let key in filters) {
                    var scriptTag = document.createElement("script");
                    var src = "";

                    // if quix loaded
                    if (window.QUIX_URL) {
                        src = window.QUIX_URL + filters[key].replace(/\s/g, '');
                    }

                    scriptTag.src = src;
                    document.head.appendChild(scriptTag);
                }
            }

            // loading inline script
            var inlineScript = document.createElement("script");

            inlineScript.innerHTML = script;

            // if script already exists
            // then remove it
            if (mountID) {
                id = mountID.replace("#", "");
                id = id + '-preview';
            } else {
                id = templateID.replace("#", "");
                id = id + '-preview';
            }
            inlineScript.id = id;
            
            if(document.querySelector("script#" + id)) document.querySelector("script#" + id).remove();

            // console.log("script", inlineScript)

            document.body.appendChild(inlineScript);

            // saved loaded element
            Assets.loadedElement.push(templateID);
        }



        /////////////////////////////////////////////////////////
        //
        // rendering html template the given templateId and data
        //
        /////////////////////////////////////////////////////////
        Assets.render = function (templateID, data, mountID) {

            if (templateID.indexOf("#") == -1) {
                return twig({
                    allowInlineIncludes: true,
                    data: templateID
                })
                    .render(Object.assign(data, { $: $, _: _, Assets, FILE_MANAGER_ROOT_URL }));
            }

            var style = Assets.style(templateID, data, mountID),
                id = Math.random().toFixed(4);

            var html = Assets.html(templateID, data)

            if (_.isObject(html)) {
                html.then(function (html) {
                    var output = html

                    if (!mountID) return output;

                    // loading style and html
                    $("div" + mountID).html(output);

                    // loading script
                    Assets.script(templateID, data);
                })
            } else {
                var output = "" +
                    "<div id='" + templateID + "-" + id + "'>" +
                    "<div>" +
                    html +
                    "</div>" +
                    "</div>";

                if (!mountID) return output;

                // loading style and html
                $("div" + mountID).html(output);

                // loading script
                Assets.script(templateID, data);
            }
        }



        /////////////////////////////////////////////////////////
        //
        // calling init
        //
        /////////////////////////////////////////////////////////
        Assets.init();



        /////////////////////////////////////////////////////////
        //
        // assigning Assets object to the Global object
        //
        /////////////////////////////////////////////////////////
        return root.Assets = Assets;

    }(

        /////////////////////////////////////////////////////////
        //
        // determine jQuery existence
        //
        /////////////////////////////////////////////////////////
        window.jQuery ? window.jQuery : undefined,

        /////////////////////////////////////////////////////////
        //
        // determine lodash existence
        //
        /////////////////////////////////////////////////////////
        window._ ? window._ : undefined,

        /////////////////////////////////////////////////////////
        //
        // determine twigjs existence
        //
        /////////////////////////////////////////////////////////
        window.twig ?


            /////////////////////////////////////////////////////////
            //
            // if twigjs loaded,
            // return instance of the loaded twigjs
            //
            /////////////////////////////////////////////////////////
            window.twig : (function () {
                console.warn("Unable to load Twig engine. Please contact support@themexpert.com");

                return undefined;
            }())
    ));
}
