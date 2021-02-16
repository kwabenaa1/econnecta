<QuixTemplate id="section-template">
  <QuixStyle>
    <!-- Global Style -->
    <?php echo file_get_contents(__DIR__ . "/../../global.twig") ?>
    <!-- Section Style -->
    <?php echo file_get_contents(__DIR__ . "/partials/style.twig") ?>
    <!-- Section Script -->
    <?php echo file_get_contents(__DIR__ . "/partials/script.twig") ?>
  </QuixStyle>
</QuixTemplate> 