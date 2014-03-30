=== PHP Shortcode ===
Contributors: godfreykfc
Donate link: 
Tags: post, pages, posts, code, php
Requires at least: 2.5
Tested up to: 2.8.3
Stable tag: 1.3

Based on kukukuan's Inline PHP plugin, this plugin allows you to embed and run PHP code in posts, pages or widgets with a WordPress shortcode.

== Description ==

Based on kukukuan's [Inline PHP][1], this plugin allows you to embed and run PHP code in posts, pages or widgets* with a WordPress shortcode.

(* Requires a shortcode enabled widget plugin, such as [Section Widget][2].)

= Usage =

The plugin provides two pairs of shortcodes - `[php]code[/php]` and `[echo]code[/echo]`. these two pairs of shortcodes resembles the functionality of the `<?php code ?>` and `<?= code ?>` tags in a normal PHP script, respectively.


For example:

    The answer to the <em>ultimate</em> math challenge, <strong>1+2</strong>, is...
    [php]
      $a = 1;
      $b = 2;
      
      echo $a + $b;
    [/php]



Will become:

    The answer to the <em>ultimate</em> math challenge, <strong>1+2</strong>, is...
    3



The `[echo]` tag will automatically print the returned value of the first expression. Therefore, `[echo]some_function()[/echo]` is essentially equivalent to `[php]echo some_function()[/php]`.


= Some Important Notes =

** This plugin will change the priority of the `do_shortcode` filter. If you are experiencing any conflict with other shortcode plugins, please disable the plugin and report the problem in the forums. **

Although I said the shortcode pairs resembles a `<?php code ?>` tag pair, there is an important difference. The PHP code in the shortcodes are executed in a "throw-away" local namespace, instead of the global one. All variables defined in a `[php] code [/php]` block **cannot** be accessed outside the block. Therefore, this will not work:

    [php]
      $a = 1;
      $b = 2;
    [/php]
    
    The answer to the <em>ultimate</em> math challenge, <strong>1+2</strong>, is... [echo]$a+$b[/echo]



And neither would this:

    [php]
      $my_array = array('apple','orange');
      
      foreach($my_array as $fruit):
    [/php]
    
    I like [echo]$fruit[/echo]
    
    [php]endforeach;[/php]



To work around the first problem, you may use the `global` keyword:

    [php]
      global $a, $b;
      $a = 1;
      $b = 2;
    [/php]

    The answer to the <em>ultimate</em> math challenge, <strong>1+2</strong>, is... [php]global $a, $b; echo $a+$b[/php]



And to work around the second problem, you may use "real" PHP closing tags within your `[php] code [/php]` block to switch between PHP and HTML mode:

    [php]
      $my_array = array('apple','orange');

      foreach($my_array as $fruit):
    ?>

    I like <?php echo $fruit; ?>

    <?php
      endforeach;
    [/php]



(Yes, it is a bit weird... you'd probably want to avoid doing that if possible.)

 [1]: http://wordpress.org/extend/plugins/inline-php/
 [2]: http://wordpress.org/extend/plugins/section-widget/

== Installation ==

1. Extract the zip file and drop the contents in the wp-content/plugins/ directory of your WordPress installation
2. Activate the Plugin from Plugins page
