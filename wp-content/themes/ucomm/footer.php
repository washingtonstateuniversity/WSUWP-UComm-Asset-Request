<?php get_template_part( 'spine' ); ?>

</div><!--/cover-->
</div><!--/jacket-->

<?php get_template_part('parts/contact'); ?> 

<?php wp_footer(); ?>
<script>
  $(function() {
    $( "#tabs" ).tabs();
});
    $( "#mainAccordion" ).accordion({
      collapsible: true,
      heightStyle: "content",
});  
  </script>
</body>
</html>