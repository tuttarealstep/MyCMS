<?php
/**
 * User: tuttarealstep
 * Date: 06/04/17
 * Time: 20.45
 *
 *
 * Thanks to https://github.com/NextStepWebs/simplemde-markdown-editor
 * Thanks to https://github.com/domchristie/to-markdown
 * Thanks to https://github.com/showdownjs/showdown
 */


/**
 * Class simpleMarkdown
 */

include_once "Parsedown.php";

class simpleMarkdown
{
    private $container;

    function __construct($container)
    {
        $this->container = $container;

        $this->initialize();
    }

    function initialize()
    {
        $this->container['plugins']->addEvent('myPageAddonsMenu', [$this, 'addMyPageAddOn']);
        $this->container['plugins']->addEvent('blogAddonsMenu', [$this, 'addMyPageAddOn']);

        $this->container['plugins']->addEvent('myPageNewEditAfterTopBar', [$this, 'setJsAndCss']);
        $this->container['plugins']->addEvent('postsNewEditAfterTopBar', [$this, 'setJsAndCss']);

        $this->container['plugins']->addEvent('myPageNewEditBeforeFooter', [$this, 'setPagesJsInit']);
        $this->container['plugins']->addEvent('postsNewEditBeforeFooter', [$this, 'setPostsJsInit']);

        $this->container['plugins']->addEvent('parseMyPageContent', function ($content) {
            $Parsedown = new Parsedown();

            return $Parsedown->text($content);

        });

        $this->container['plugins']->addEvent('parsePostContent', function ($content) {
            $Parsedown = new Parsedown();

            return $Parsedown->text($content);

        });
    }

    function setJsAndCss()
    {
        ?>
        <link rel="stylesheet" href="{@MY_PLUGINS_PATH@}/simpleMarkdown/css/simplemde.min.css">
        <script src="{@MY_PLUGINS_PATH@}/simpleMarkdown/js/simplemde.min.js"></script>
        <script src="{@MY_PLUGINS_PATH@}/simpleMarkdown/js/to-markdown.js"></script>
        <script src="{@MY_PLUGINS_PATH@}/simpleMarkdown/js/showdown.min.js"></script>
        <?php
    }

    function addMyPageAddOn()
    {
        ?>
        <a id="simpleMarkdownButton" class="default-addon" href="#textareaContent" onclick="switchToMDE();">Markdown</a>
        <?php
    }

    function setPagesJsInit()
    {
        ?>
        <script>
            var mcVisible = true;
            var simplemde = null;

            function switchToMDE() {
                if (mcVisible) {
                    mcVisible = false;
                    tinyMCE.execCommand("mceRemoveEditor", true, tinymce.editors[0].id);
                    simplemde = new SimpleMDE({element: document.getElementById("pages_content"), forceSync: true});
                    simplemde.value(toMarkdown(document.getElementById("pages_content").value));

                } else {
                    mcVisible = true;

                    simplemde.toTextArea();
                    simplemde = null;

                    var converter = new showdown.Converter();
                    document.getElementById("pages_content").value = converter.makeHtml(document.getElementById("pages_content").value);


                    /* var element = document.createElement("textarea");
                     element.id = "pages_content";
                     element.name = "pages_content";
                     element.style.height = "300px";
                     document.getElementById("textareaContent").appendChild(element);*/

                    tinymce.init({
                        selector: "textarea",
                        language_url: '{@siteURL@}/src/App/MyAdmin/languages/{@siteLANGUAGE@}.js',
                        plugins: [
                            "advlist autolink lists link image charmap print preview anchor",
                            "searchreplace visualblocks code fullscreen",
                            "insertdatetime media table contextmenu paste textcolor"
                        ],

                        toolbar: "insertfile undo redo | styleselect forecolor backcolor |  bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                        autosave_ask_before_unload: false,
                        relative_urls : false,
                        remove_script_host: false
                    });
                }
            }
        </script>
        <?php
    }

    function setPostsJsInit()
    {
        ?>
        <script>
            var mcVisible = true;
            var simplemde = null;

            function switchToMDE() {
                if (mcVisible) {
                    mcVisible = false;
                    tinyMCE.execCommand("mceRemoveEditor", true, tinymce.editors[0].id);
                    simplemde = new SimpleMDE({element: document.getElementById("postContent"), forceSync: true});
                    simplemde.value(toMarkdown(document.getElementById("postContent").value));

                } else {
                    mcVisible = true;

                    simplemde.toTextArea();
                    simplemde = null;

                    var converter = new showdown.Converter();
                    document.getElementById("postContent").value = converter.makeHtml(document.getElementById("postContent").value);

                    tinymce.init({
                        selector: "textarea",
                        language_url: '{@siteURL@}/src/App/MyAdmin/languages/{@siteLANGUAGE@}.js',
                        plugins: [
                            "advlist autolink lists link image charmap print preview anchor",
                            "searchreplace visualblocks code fullscreen",
                            "insertdatetime media table contextmenu paste textcolor"
                        ],

                        toolbar: "insertfile undo redo | styleselect forecolor backcolor |  bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                        autosave_ask_before_unload: false,
                        relative_urls : false,
                        remove_script_host: false
                    });
                }
            }
        </script>
        <?php
    }
}

new simpleMarkdown($this->container);