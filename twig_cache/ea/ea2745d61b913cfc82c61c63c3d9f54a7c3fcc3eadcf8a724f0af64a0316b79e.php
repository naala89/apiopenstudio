<?php

/* install_3.html */
class __TwigTemplate_357865a8bea8197e706eda06370f9b9f282dc325b4a0dc3c64a0333d8132333a extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("install.html", "install_3.html", 1);
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
        $context["title"] = "Step 3: Create your application";
        // line 1
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "
<div class=\"row margin-top-medium\">
  <form class=\"uk-form-horizontal\" method=\"post\" action=\"install.php\">
    <div class=\"uk-grid\">

      <div class=\"uk-width-1-3\"></div>

      <div class=\"uk-width-1-3\">

        <input type=\"hidden\" name=\"from_step\" value=\"3\">
        <input type=\"hidden\" name=\"next_step\" value=\"3\">
        <input type=\"hidden\" name=\"uid\" value=\"";
        // line 17
        echo twig_escape_filter($this->env, ($context["uid"] ?? null), "html", null, true);
        echo "\">

        <div class=\"uk-margin\">
          <p>This will be the name of of your account.</p>
          <p>An account can have multiple applications.</p>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"account_name\">Account name <span class=\"uk-text-danger\">*</span></label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"account_name\" name=\"account_name\" type=\"text\" placeholder=\"\" required>
          </div>
        </div>

        <div class=\"uk-margin\">
          <div class=\"uk-form-controls\">
            <button class=\"uk-button uk-button-success uk-form-width-medium\" type=\"submit\">Submit</button>
          </div>
        </div>

      </div>
      <div class=\"uk-width-1-3\"></div>
    </div>
  </form>
</div>
";
    }

    public function getTemplateName()
    {
        return "install_3.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  51 => 17,  38 => 6,  35 => 5,  31 => 1,  29 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "install_3.html", "/var/www/sites/datagator/includes/admin/templates/install/install_3.html");
    }
}
