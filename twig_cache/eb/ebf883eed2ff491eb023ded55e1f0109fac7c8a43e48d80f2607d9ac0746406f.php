<?php

/* install_4.html */
class __TwigTemplate_b262691c86e77625db9b4fce639afb79750f655e67aa56cbee0f998d592ac89d extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("install.html", "install_4.html", 1);
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
        $context["title"] = "Step 4: Install complete";
        // line 1
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "
<div class=\"row margin-top-medium\">
  <form class=\"uk-form-horizontal\" method=\"post\" action=\"index.php\">
    <div class=\"uk-grid\">

      <div class=\"uk-width-1-3\"></div>

      <div class=\"uk-width-1-3\">
        <legend class=\"uk-legend\">Congratulations install complete!</legend>
        <div class=\"uk-margin\">
          <p>You have been granted the `Owner` role for ";
        // line 16
        echo twig_escape_filter($this->env, ($context["account_name"] ?? null), "html", null, true);
        echo ".</p>
          <p>This gives you permissions to create accounts and grant administrator access to other users for your accounts.</p>
          <p>Click continue to go to the admin area to set up your applications and administrators.</p>
        </div>
        <div class=\"uk-margin\">
          <button class=\"uk-button uk-button-success uk-form-width-medium\" type=\"submit\">Continue</button>
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
        return "install_4.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 16,  38 => 6,  35 => 5,  31 => 1,  29 => 3,  15 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "install_4.html", "/var/www/sites/datagator/includes/admin/templates/install/install_4.html");
    }
}
