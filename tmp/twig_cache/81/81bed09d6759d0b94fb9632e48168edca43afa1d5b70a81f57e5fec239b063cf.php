<?php

/* install_0.html */
class __TwigTemplate_51ec7e4f67656b9327c97fc7ceb5b0ccea50e008a892b47c2a55270186ea609c extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("install.html", "install_0.html", 1);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "install.html";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 3
        $context["title"] = "Step 0: Install Gaterdata";
        // line 1
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "<div class=\"uk-grid\">
  <div class=\"uk-width-1-3\"></div>
  <div class=\"uk-width-1-3\">
    <form class=\"uk-form\" method=\"post\">
      <input type=\"hidden\" name=\"from_step\" value=\"0\">
      <input type=\"hidden\" name=\"next_step\" value=\"1\">
      <fieldset data-uk-margin>
        <legend>Continue?</legend>
        <div class=\"uk-button-group uk-width-1-1\">
          <button class=\"uk-button uk-button-danger uk-width-1-2\" type=\"submit\" value=\"no\" formaction=\"/admin/install.php\">Yes</button>
          <button class=\"uk-button uk-button-success uk-width-1-2\" type=\"submit\" value=\"no\" formaction=\"/admin/\">No</button>
        </div>
      </fieldset>
    </form>
  </div>
  <div class=\"uk-width-1-3\"></div>
</div>
";
    }

    public function getTemplateName()
    {
        return "install_0.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 6,  35 => 5,  31 => 1,  29 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "install_0.html", "/var/www/sites/datagator/includes/admin/templates/install/install_0.html");
    }
}
