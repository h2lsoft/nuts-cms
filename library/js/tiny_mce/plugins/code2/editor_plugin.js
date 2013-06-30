(function() {
  tinymce.PluginManager.requireLangPack('code2');
  
  tinymce.create('tinymce.plugins.Code2Plugin', {
    init : function(ed, url) {
      
	  
	  // Register commands
      ed.addCommand('mceCode2', function() {
		
		 ed.windowManager.open({
			file : url + '/dialog.htm',
			width : 1200 + parseInt(ed.getLang('code2.delta_width', 0)),
			height : 850 + parseInt(ed.getLang('code2.delta_height', 0)),
			inline : false
			}, 
			{
				plugin_url : url
			});
      });

      // Register buttons
      ed.addButton('code2', {
        title : ed.getLang('code2.desc', 0),
        cmd : 'mceCode2',
        image : url + '/img/code2.gif'
      });

      ed.onNodeChange.add(function(ed, cm, n) {});
    },

    getInfo : function() {
      return {
        longname : 'Advanced Code Editor',
        author : 'H2Lsoft',
        authorurl : 'http://www.nuts-cms.com',
        infourl : 'http://www.nuts-cms.com',
        version : "1.5"
      };
    }
  });

  // Register plugin
  tinymce.PluginManager.add('code2', tinymce.plugins.Code2Plugin);
})();
