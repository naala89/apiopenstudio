<?php

/* install_2.html */
class __TwigTemplate_ce4446e32cdb9df197f19717451488e42058eeae41da439bc1d2e7fd04277529 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 1
        $this->parent = $this->loadTemplate("install.html", "install_2.html", 1);
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
        $context["title"] = "Step 2: Create your user";
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

        <input type=\"hidden\" name=\"from_step\" value=\"2\">
        <input type=\"hidden\" name=\"next_step\" value=\"2\">

        <div class=\"uk-margin\">
          <p>This will create your account that you will use to login with.</p>
          <p>Your user will be the primary account that controls the account.</p>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"username\">Username <span class=\"uk-text-danger\">*</span></label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"username\" name=\"username\" type=\"text\" placeholder=\"\" required>
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"password\">Password <span class=\"uk-text-danger\">*</span></label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"password\" name=\"password\" type=\"password\" placeholder=\"\" required>
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"email\">Email</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"email\" name=\"email\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"honorific\">Title</label>
          <div class=\"uk-form-controls\">
            <select class=\"uk-select uk-form-width-medium\" id=\"honorific\" name=\"honorific\">
              <option value=\"mr\">Mr</option>
              <option value=\"ms\">Ms</option>
              <option value=\"miss\">Miss</option>
              <option value=\"mrs\">Mrs</option>
              <option value=\"dr\">Dr</option>
              <option value=\"prof\">Prof</option>
            </select>
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"name_first\">First Name</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"name_first\" name=\"name_first\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"name_last\">Last Name</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"name_last\" name=\"name_last\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"company\">Company</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"company\" name=\"company\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"website\">Website</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"website\" name=\"website\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"address_street\">Street Address</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"address_street\" name=\"address_street\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"address_suburb\">Suburb</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"address_suburb\" name=\"address_suburb\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"address_city\">City</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"address_city\" name=\"address_city\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"address_state\">State</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"address_state\" name=\"address_state\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"address_country\">Country</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"address_country\" name=\"address_country\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"address_postcode\">Postcode</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"address_postcode\" name=\"address_postcode\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"phone_mobile\">Mobile Phone</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"phone_mobile\" name=\"phone_mobile\" type=\"text\" placeholder=\"\">
          </div>
        </div>

        <div class=\"uk-margin\">
          <label class=\"uk-form-label\" for=\"phone_work\">Work Phone</label>
          <div class=\"uk-form-controls\">
            <input class=\"uk-input uk-form-width-medium\" id=\"phone_work\" name=\"phone_work\" type=\"text\" placeholder=\"\">
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
        return "install_2.html";
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
        return new Twig_Source("", "install_2.html", "/var/www/sites/datagator/includes/admin/templates/install/install_2.html");
    }
}
