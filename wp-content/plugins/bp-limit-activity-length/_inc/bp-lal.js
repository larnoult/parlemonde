;(function($){ // Closure

  $.fn.clearTextLimit = function() {
      return this.each(function() {
         this.onkeydown = this.onkeyup = null;
      });
  };
  $.fn.textLimit = function( limit , type , callback ) {
      if ( ! callback ) {
        callback = type;
      }

      if ( typeof callback !== 'function' ) var callback = function() {};
      return this.each(function() {
        this.limit = limit;
        this.callback = callback;
        this.count = 0;
        this.onkeydown = this.onkeyup = this.onfocus = function() {

          if ( type && type === 'word' ) {

            this.reached = false;
            var re = /\w+/g,
                match,
                wordCount = 0;
            while (( match = re.exec(this.value)) !== null ) {
              if ( wordCount >= this.limit ) {
                var lastChar = match.index;
                this.reached = true;
                break;
              }
              wordCount++;

            }
            this.count = wordCount;

            if ( this.value[lastChar] && this.value[lastChar].match(/[^\w\s\n\t]/) ) lastChar++;
            this.value = this.value.substr(0,lastChar);

            return this.callback( this.count, this.limit, this.reached );
          } else {
            this.onkeydown = this.onkeyup = this.onfocus = function() {
              this.value = this.value.substr(0,this.limit);
              this.reached = this.limit - this.value.length;
              this.reached = ( this.reached == 0 ) ? true : false;
              return this.callback( this.value.length, this.limit, this.reached );
            }
          }
          this.pointer = this.value.length;
        }
      });
  };

  $(document).ready(function() {
    var activity_limit = BPLal.limit,
        type           = BPLal.type;

    $("#whats-new-submit").after("<div id='whats-new-limit' class='activity-limit'></div>");
    $('textarea#whats-new').textLimit(activity_limit,type,function( length, limit ){
      $("#whats-new-limit").text( limit - length );
    }).trigger("keyup");

    $(".ac-form input[type=submit]").before("<div class='activity-limit'>" + activity_limit + "</div>");
    $(document).on("keydown", ".ac-form", function() {
      var $form = $(this);
      if ( $form.data('hasTextlimit' ) ) return;
      
      // For ajaxed in comment forms
      if ( ! $form.find(".activity-limit").get(0) )
        $form.find("input[type=submit]").before("<div class='activity-limit'>" + activity_limit + "</div>");

      $form.find("textarea").textLimit(activity_limit,type,function( length, limit ) {
        $form.find(".activity-limit").text( limit - length );
      }).trigger("keyup");

      $form.data('hasTextlimit', true);
    });

  });

})(jQuery);