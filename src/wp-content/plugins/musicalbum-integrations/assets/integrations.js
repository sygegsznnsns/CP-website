(function($){
  $(function(){
    var btn = $('#musicalbum-ocr-button');
    var file = $('#musicalbum-ocr-file');
    btn.on('click', function(){
      var f = file[0] && file[0].files && file[0].files[0];
      if(!f) return;
      var fd = new FormData();
      fd.append('image', f);
      $.ajax({
        url: MusicalbumIntegrations.rest.ocr,
        method: 'POST',
        headers: { 'X-WP-Nonce': MusicalbumIntegrations.rest.nonce },
        data: fd,
        processData: false,
        contentType: false
      }).done(function(res){
        try {
          if (window.acf && res) {
            if (res.title) $('input[name="post_title"]').val(res.title);
            if (res.theater && acf.getField('field_malbum_theater')) acf.getField('field_malbum_theater').val(res.theater);
            if (res.cast && acf.getField('field_malbum_cast')) acf.getField('field_malbum_cast').val(res.cast);
            if (res.price && acf.getField('field_malbum_price')) acf.getField('field_malbum_price').val(res.price);
            if (res.view_date && acf.getField('field_malbum_date')) acf.getField('field_malbum_date').val(res.view_date);
          }
        } catch(e) {}
      });
    });
  });
})(jQuery);
