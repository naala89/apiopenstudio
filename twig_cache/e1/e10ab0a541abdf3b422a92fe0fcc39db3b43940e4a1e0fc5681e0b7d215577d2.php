<?php

/* install_1.html */
class __TwigTemplate_828e298e5a8a470208185284cce07a95ff4b5d76b7ed5a59c25f60d8ad523dd6 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("install.html", "install_1.html", 1);
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
        $context["title"] = "Step 1: Setup the DB";
        // line 1
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "<div class=\"row margin-top-medium\">
  <form class=\"uk-form\" method=\"post\">
    <div class=\"uk-grid\">
      <div class=\"uk-width-1-3\"></div>
      <div class=\"uk-width-1-3\">
        <form class=\"uk-form\" method=\"post\">
          ";
        // line 12
        if ((twig_get_attribute($this->env, $this->source, ($context["message"] ?? null), "type", array()) == "error")) {
            // line 13
            echo "            <input type=\"hidden\" name=\"from_step\" value=\"1\">
            <input type=\"hidden\" name=\"next_step\" value=\"2\">
              <fieldset data-uk-margin>
                <legend>Database creation failed. Retry?</legend>
                <div class=\"uk-button-group uk-width-1-1\">
                  <button class=\"uk-button uk-button-danger uk-width-1-2\" type=\"submit\" value=\"no\" formaction=\"/admin/install.php\">Yes</button>
                  <button class=\"uk-button uk-button-success uk-width-1-2\" type=\"submit\" value=\"no\" formaction=\"/admin/\">No</button>
                </div>
              </fieldset>
          ";
        } else {
            // line 23
            echo "            <input type=\"hidden\" name=\"from_step\" value=\"1\">
            <input type=\"hidden\" name=\"next_step\" value=\"2\">
            <fieldset data-uk-margin>
              <legend>Database successfully created!</legend>
              <div class=\"uk-button-group uk-width-1-1\">
                <button class=\"uk-button uk-button-success uk-width-1-2 uk-align-center\" type=\"submit\" formaction=\"/admin/install.php\">Continue</button>
              </div>
            </fieldset>
          ";
        }
        // line 32
        echo "        </form>
      </div>
      <div class=\"uk-width-1-3\"></div>
    </div>
  </form>
</div>
";
    }

    public function getTemplateName()
    {
        return "install_1.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 32,  60 => 23,  48 => 13,  46 => 12,  38 => 6,  35 => 5,  31 => 1,  29 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "install_1.html", "/var/www/sites/datagator/includes/admin/templates/install/install_1.html");
    }
}
