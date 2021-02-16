/**
 * @version $Id: djselect.js 6 2015-08-11 23:31:14Z szymon $
 * @package DJ-Menu
 * @copyright Copyright (C) 2012 DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 */
(function($){function addOptionsFromDJMenu(f,g,h){var j='',active=false;for(var i=0;i<h;i++){j+='- '}f.each(function(a){var b=a.getElement('a');var c=a.getElement('ul');if(b){var d='';if(img=b.getElement('img')){d=img.get('alt')}else{d=b.get('text')}var e=new Element('option',{value:b.get('href'),text:j+d}).inject(g);if(!b.get('href')){e.set('disabled',true)}if(a.hasClass('active')){g.set('value',e.get('value'));active=true}}if(c)addOptionsFromDJMenu(c.getChildren('li'),g,h+1)});if(!h&&!active){new Element('option',{value:'',text:'- MENU -'}).inject(g,'top');g.set('value','')}}window.addEvent('load',function(){$$('.dj-main').each(function(a){var b=new Element('select',{id:a.get('id')+'select','class':'inputbox dj-select',events:{change:function(){if(this.get('value'))window.location=this.get('value')}}});var c=a.getChildren('li.dj-up');addOptionsFromDJMenu(c,b,0);b.inject(a,'after')})})})(document.id);