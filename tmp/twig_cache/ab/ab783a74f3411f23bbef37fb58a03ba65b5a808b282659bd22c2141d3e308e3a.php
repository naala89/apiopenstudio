<?php

/* install.html */
class __TwigTemplate_6c192a0acb6b8697d15182cae21e14776149e89812be9a961fe35e4d0b3d246a extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "
<!DOCTYPE html>
<html lang=\"en-gb\" dir=\"ltr\">

<head>
  <meta charset=\"utf-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
  <title>Gaterdata Install</title>
  <link rel=\"shortcut icon\" href=\"images/favicon.ico\" type=\"image/x-icon\">
  <link rel=\"apple-touch-icon-precomposed\" href=\"images/apple-touch-icon.png\">
  <link rel=\"stylesheet\" href=\"css/uikit.docs.min.css\">
  <script src=\"../vendor/components/jquery/jquery.min.js\"></script>
  <script src=\"js/uikit.min.js\"></script>
</head>

<body>

<div class=\"uk-container uk-container-center uk-margin-top uk-margin-large-bottom\">

  <nav class=\"uk-navbar uk-margin-large-bottom\">
    <a class=\"uk-navbar-brand uk-hidden-small\" href=\"/admin/\">Gaterdata</a>
    <ul class=\"uk-navbar-nav uk-hidden-small\">
      ";
        // line 23
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["menu"] ?? null));
        foreach ($context['_seq'] as $context["key"] => $context["value"]) {
            // line 24
            echo "      <li>
        <a href=\"";
            // line 25
            echo twig_escape_filter($this->env, $context["value"], "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $context["key"], "html", null, true);
            echo "</a>
      </li>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 28
        echo "    </ul>
    <a href=\"#offcanvas\" class=\"uk-navbar-toggle uk-visible-small\" data-uk-offcanvas></a>
    <div class=\"uk-navbar-brand uk-navbar-center uk-visible-small\">Gaterdata</div>
  </nav>

  <div class=\"uk-grid\" data-uk-grid-margin>
    <div class=\"uk-width-medium-1-1\">
      <div class=\"uk-vertical-align uk-text-center\" style=\"background: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxNi4wLjQsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkViZW5lXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB3aWR0aD0iMTEzMHB4IiBoZWlnaHQ9IjQ1MHB4IiB2aWV3Qm94PSIwIDAgMTEzMCA0NTAiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMCAwIDExMzAgNDUwIiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxyZWN0IGZpbGw9IiNGNUY1RjUiIHdpZHRoPSIxMTMwIiBoZWlnaHQ9IjQ1MCIvPg0KPC9zdmc+DQo=') 50% 0 no-repeat; \">
        <div class=\"uk-vertical-align-middle uk-width-1-2\">
          <h1 class=\"uk-heading-large\">";
        // line 37
        echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
        echo "</h1>
        </div>
      </div>
    </div>
  </div>

  ";
        // line 43
        if ((isset($context["message"]) || array_key_exists("message", $context))) {
            // line 44
            echo "    ";
            if ((twig_get_attribute($this->env, $this->source, ($context["message"] ?? null), "type", array()) == "info")) {
                // line 45
                echo "      ";
                $context["message_class"] = "uk-alert-success";
                // line 46
                echo "    ";
            }
            // line 47
            echo "    ";
            if ((twig_get_attribute($this->env, $this->source, ($context["message"] ?? null), "type", array()) == "warning")) {
                // line 48
                echo "      ";
                $context["message_class"] = "uk-alert-warning";
                // line 49
                echo "    ";
            }
            // line 50
            echo "    ";
            if ((twig_get_attribute($this->env, $this->source, ($context["message"] ?? null), "type", array()) == "error")) {
                // line 51
                echo "      ";
                $context["message_class"] = "uk-alert-danger";
                // line 52
                echo "    ";
            }
            // line 53
            echo "    <div class=\"";
            echo twig_escape_filter($this->env, ($context["message_class"] ?? null), "html", null, true);
            echo " uk-width-1-2 uk-align-center\" data-uk-alert>
      <a href=\"\" class=\"uk-alert-close uk-close\"></a>
      <h3>";
            // line 55
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["message"] ?? null), "type", array()), "html", null, true);
            echo "</h3>
      <p>";
            // line 56
            echo twig_get_attribute($this->env, $this->source, ($context["message"] ?? null), "text", array());
            echo "</p>
    </div>
  ";
        }
        // line 59
        echo "
  ";
        // line 60
        $this->displayBlock('content', $context, $blocks);
        // line 61
        echo "
  <footer>
    ";
        // line 63
        $this->displayBlock('footer', $context, $blocks);
        // line 66
        echo "  </footer>

</div>

</body>
</html>";
    }

    // line 60
    public function block_content($context, array $blocks = array())
    {
    }

    // line 63
    public function block_footer($context, array $blocks = array())
    {
        // line 64
        echo "    &copy; Copyright 2018 by <a href=\"http://datagator.com/\">Datagator</a>.
    ";
    }

    public function getTemplateName()
    {
        return "install.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  160 => 64,  157 => 63,  152 => 60,  143 => 66,  141 => 63,  137 => 61,  135 => 60,  132 => 59,  126 => 56,  122 => 55,  116 => 53,  113 => 52,  110 => 51,  107 => 50,  104 => 49,  101 => 48,  98 => 47,  95 => 46,  92 => 45,  89 => 44,  87 => 43,  78 => 37,  67 => 28,  56 => 25,  53 => 24,  49 => 23,  25 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "install.html", "/var/www/sites/datagator/includes/admin/templates/install/install.html");
    }
}
