<?php
namespace Frontend\Core\Engine;

use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Twig node for writing out a compiled version of a closing form tag.
 *
 * @author <per@wijs.be>
 */
class EndformNode extends \Twig_Node
{
   /**
    * @param int $lineno Line number in the template source file.
    * @param string $tag
    */
   public function __construct($lineno, $tag)
   {
       parent::__construct(array(), array(), $lineno, $tag);
   }

   /**
    * @param Twig_Compiler $compiler
    */
   public function compile(\Twig_Compiler $compiler)
   {
       $compiler
           ->addDebugInfo($this)
           ->write('echo \'</form>\';');
   }
}

/**
 * Twig token parser for form closing tag.
 *
 * @author <per@wijs.be>
 */
class EndformTokenParser extends \Twig_TokenParser
{
   /**
    * @param Twig_Token $token Token consumed by the lexer.
    * @return Twig_Node
    * @throw Twig_Error_Syntax
    */
   public function parse(\Twig_Token $token)
   {
       $stream = $this->parser->getStream();
       if($stream->getCurrent()->getType() != \Twig_Token::BLOCK_END_TYPE)
       {
           $error = sprintf("'%s' does not require any arguments.", $this->getTag());
           throw new \Twig_Error_Syntax($error, $token->getLine(), $this->parser->getFilename());
       }
       $stream->expect(\Twig_Token::BLOCK_END_TYPE);

       if(FormState::$current === null)
       {
           throw new \Twig_Error_Syntax(
               'Trying to close a form tag, while none opened',
               $token->getLine(),
               $this->parser->getFilename()
           );
       }
       else
       {
           FormState::$current = null;
       }
       return new EndformNode($token->getLine(), $this->getTag());
   }

   /**
    * @return string
    */
   public function getTag()
   {
       return 'endform';
   }
}

/**
 * Twig node for writing out the compiled version of form field.
 *
 * @author <per@wijs.be>
 */
class FormFieldNode extends \Twig_Node
{
   private $form;
   private $field;

   /**
    * @param string $form Name of the template var holding the form this field
    *                     belongs to.
    * @param string $field Name of the field to render.
    * @param int $lineno Line number in the template source file.
    * @param string $tag
    */
   public function __construct($form, $field, $lineno, $tag)
   {
       parent::__construct(array(), array(), $lineno, $tag);
       $this->form = $form;
       $this->field = $field;
   }

   /**
    * @param Twig_Compiler $compiler
    */
   public function compile(\Twig_Compiler $compiler)
   {
       $parseField = "\$context['form_{$this->form}']->getField('{$this->field}')->parse()";
       $compiler
           ->addDebugInfo($this)
           ->write("echo $parseField;\n")
       ;
   }
}

/**
 * Twig token parser for form fields.
 *
 * @author <per@wijs.be>
 */
class FormFieldTokenParser extends \Twig_TokenParser
{
   /**
    * @param Twig_Token $token consumed token by the lexer.
    * @return Twig_Node
    * @throw Twig_Error_Syntax
    */
   public function parse(\Twig_Token $token)
   {
       $stream = $this->parser->getStream();
       $field = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
       $stream->expect(\Twig_Token::BLOCK_END_TYPE);
       if(FormState::$current === null)
       {
           throw new \Twig_Error_Syntax(
               sprintf('Cannot render form field [%s] outside a form element', $field),
               $token->getLine(),
               $this->parser->getFilename()
           );
       }
       return new FormFieldNode(FormState::$current, $field, $token->getLine(), $this->getTag());
   }

   /**
    * @return string
    */
   public function getTag()
   {
       return 'form_field';
   }
}


/**
 * Twig note for writing out the compiled version of a form field error.
 *
 * @author <per@wijs.be>
 */
class FormFieldErrorNode extends \Twig_Node
{
   private $form;
   private $field;

   /**
    * @param string $form Name of the template var holding the form this field
    *                     error belongs to.
    * @param string $field Name of the field of which we need to render the error.
    * @param int $lineno Line number in the template source file.
    * @param string $tag the name of the template tag.
    */
   public function __construct($form, $field, $lineno, $tag)
   {
       parent::__construct(array(), array(), $lineno, $tag);
       $this->form = $form;
       $this->field = $field;
   }

   public function compile(Twig_Compiler $compiler)
   {
       $writeErrorMessage = "echo "
           . "\$context['form_{$this->form}']->getField('{$this->field}')->getErrors() "
           . "? '<span class=\"formError\">' "
           . ". \$context['form_{$this->form}']->getField('{$this->field}')->getErrors() "
           . ". '</span>' : '';";
       $compiler
           ->addDebugInfo($this)
           ->write($writeErrorMessage)
       ;
   }
}

/**
 * Twig token parser for form field errors.
 *
 * @author <per@wijs.be>
 */
class FormFieldErrorTokenParser extends \Twig_TokenParser
{
   /**
    * @param Twig_Token $token consumed token by the lexer.
    * @return Twig_Node
    * @throw Twig_Error_Syntax
    */
   public function parse(Twig_Token $token)
   {
       $stream = $this->parser->getStream();
       $field = $stream->expect(Twig_Token::NAME_TYPE)->getValue();
       $stream->expect(Twig_Token::BLOCK_END_TYPE);
       if(FormState::$current === null)
       {
           throw new \Twig_Error_Syntax(
               sprintf('Cannot render form field error [%s] outside a form element', $field),
               $token->getLine(),
               $this->parser->getFilename()
           );
       }
       return new FormFieldErrorNode(
           FormState::$current, $field, $token->getLine(), $this->getTag());
   }

   /**
    * @return string
    */
   public function getTag()
   {
       return 'form_field_error';
   }
}

/**
 * Keeps the state of a form between opening and closing the tag.
 * Since forms cannot be nested, we can resort to a quick 'n dirty yes/no global state.
 *
 * In the future we could remove it and use stack {push,popp}ing, but I'm hoping
 * we're using symfony forms or some other more OO form library.
 *
 * @author <per@wijs.be>
 */
class FormState
{
   public static $current = null;
}

/**
 * Twig node for writing out the compiled representation of an opeing form tag.
 *
 * @author <per@wijs.be>
 */
class FormNode extends \Twig_Node
{
   /**
    * @var string Template variable holding the form.
    */
   private $form;

   /**
    * @param string $form The name of the template variable to which the form is assigned
    * @param int $lineno
    * @param string $tag
    */
   public function __construct($form, $lineno, $tag)
   {
       parent::__construct(array(), array(), $lineno, $tag);
       $this->form = $form;
   }

   /**
    * @param Twig_Compiler $compiler
    */
   public function compile(\Twig_Compiler $compiler)
   {
       // Set some string representations to make the code writing via the
       // compiler a bit more readable. ("a bit")
       $frm = "\$context['form_{$this->form}']";
       $frmAction = $frm . '->getAction()';
       $frmMethod = $frm . '->getMethod()';
       $frmName = $frm . '->getName()';
       $frmToken = $frm . '->getToken()';
       $frmUseToken = $frm . '->getUseToken()';
       $frmParamsHtml = $frm . '->getParametersHTML()';
       $frmAttrAction = ' action="\', ' . $frmAction . ', \'"';
       $frmAttrMethod = ' method="\', ' . $frmMethod . ', \'"';
       $hiddenFormName = '<input type="hidden" name="form" value="\', ' . $frmName . ', \'" id="form\', ucfirst(' . $frmName . '), \'" />';
       $hiddenFormToken = '<input type="hidden" name="form_token" value="\', ' . $frmToken . ', \'" id="formToken\', ucfirst(' . $frmName . '), \'" />';

       // oh boy,  disabled atm constant is gone
       // $htmlAcceptCharset = (SPOON_CHARSET == 'utf-8')
       //     ? ' accept-charset="UTF-8"'
       //     : '';

       $compiler
           ->addDebugInfo($this)

           ->write('echo \'<form')
           ->raw($frmAttrMethod)
           ->raw($frmAttrAction)
           //->raw($htmlAcceptCharset)
           ->raw("', ")
           ->raw(' ' . $frmParamsHtml)
           ->raw(', \'')
           ->raw('>\'')
           ->raw(";\n")

           ->write("echo '$hiddenFormName';\n")
           ->write("if($frmUseToken) echo '$hiddenFormToken';")
       ;
   }
}

/**
 * Twig template tag for the start/opening element of a form tag.
 *
 * @author <per@wijs.be>
 */
class FormTokenParser extends \Twig_TokenParser
{
   /**
    * @param Twig_Token $token
    * @return Twig_Node
    * @throw Twig_Error_Syntax
    */
   public function parse(\Twig_Token $token)
   {
       $stream = $this->parser->getStream();
       $form = $stream->expect(\Twig_Token::NAME_TYPE)->getValue();
       $stream->expect(\Twig_Token::BLOCK_END_TYPE);

       if(FormState::$current !== null)
       {
           throw new \Twig_Error_Syntax(
               sprintf(
                   'form [%s] not closed while opening form [%s]',
                   FormState::$current,
                   $form
               ),
               $token->getLine(),
               $stream->getFilename()
           );
       }
       else
       {
           FormState::$current = $form;
       }

       return new FormNode($form, $token->getLine(), $this->getTag());
   }

   /**
    * @return string
    */
   public function getTag()
   {
       return 'form';
   }
}

class FormExtension
{
    /**
     * @param Object Twig
     */
    protected $twig;

    /**
     * Create a new Form Twig_Extension instance.
     *
     * @param
     */
    public function __construct($twig)
    {
        $this->twig = $twig;

        // option only on forms
        $this->twig->addTokenParser(new FormTokenParser());
        $this->twig->addTokenParser(new EndformTokenParser());
        $this->twig->addTokenParser(new FormFieldTokenParser());
        $this->twig->addTokenParser(new FormFieldErrorTokenParser());
    }
}
