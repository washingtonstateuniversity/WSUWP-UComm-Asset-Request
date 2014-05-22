<?php get_template_part( 'spine' ); ?>

</div><!--/cover-->
</div><!--/jacket-->

<?php get_template_part('parts/contact'); ?> 

<?php wp_footer(); ?>
<script>
 jQuery('#tabs').tabs({
  fx: { opacity: 'toggle' },
  select: function(event, ui) {
    jQuery(this).css('height', jQuery(this).height());
    jQuery(this).css('overflow', 'hidden');
  },
  show: function(event, ui) {
    jQuery(this).css('height', 'auto');
    jQuery(this).css('overflow', 'visible');
  }
});
$( "#mainAccordion" ).accordion({
     heightStyle: "content",
});  
$(function() {
  $('a[href*=#]:not([href=#])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html,body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });
});
 $(document).ready(function(){
     $(window).bind('scroll', function() {
     var callHeight = $( window ).height() - 70;
       if ($(window).scrollTop() > callHeight) {
         $('.fixme').addClass('fixed');
       }
       else {
         $('.fixme').removeClass('fixed');
       }
    });
  });
  </script>
</body>
</html>