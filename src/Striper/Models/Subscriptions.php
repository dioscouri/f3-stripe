<?php
namespace Striper\Models;

class Subscriptions extends \Dsc\Mongo\Collections\Describable
{
   
 	public $client = array();
 	public $copy = null;
   
    protected $__collection_name = 'striper.subscriptions';
    protected $__type = 'striper.subscriptions';

    protected function fetchConditions()
    {
        parent::fetchConditions();

       
            
        return $this;
    }
    
    
    public function amount()
    {
        $amount = $this->amount;
        
        // TODO ensure that the $amount value is a float, if necessary, 
        // and if a decimal is present, convert it to whatever format stripe requires

        return $amount;
    }
    
    public function amountForStripe()
    {
        $amount = $this->amount;
    
        // ensure that the $amount value is a float, if necessary,
        // and if a decimal is present, convert it to whatever format stripe requires
        if (strpos($amount, '.') !== false) {
            $amount = $amount * 100;
        }
    
        return $amount;
    }
    
    
    public function  acceptPayment($charge) {
    	//update the document that we got paid
    	$this->status = 'PAID';
    	$this->charge = array('id' => $charge->id, 'created' =>  $charge->created);
    	$this->save();
    }
    

    protected function beforeValidate()
    {
        return parent::beforeValidate();
    }

    public function validate()
    {
       
        return parent::validate();
    }


    protected function beforeSave()
    {
        $this->amount = (float) $this->amount;
        
        foreach ($this->items as $key=>$item) 
        {
        	if (empty($item['description']) && empty($item['quantity']) && empty($item['rate'])) 
        	{
        		unset($this->items[$key]);
        	}
        }
        $this->items = array_values($this->items);
        
        return parent::beforeSave();
    }
    
    public function sendChargeEmailClient($charge){
   
    	\Base::instance()->set('charge', $charge);
    	
     $html = \Dsc\System::instance()->get( 'theme' )->renderView( 'Striper/Site/Views::subscription/emails/client_success_html.php' );
     $text = \Dsc\System::instance()->get( 'theme' )->renderView( 'Striper/Site/Views::subscription/emails/client_success_text.php' );
         	
     \Dsc\System::instance()->get('mailer')->send($charge->{'client.email'}, 'Successfully Subscribed', array($html, $text) );
    	 
    }
    
    public function sendChargeEmailAdmin($charge){
     $html = \Dsc\System::instance()->get( 'theme' )->renderView( 'Striper/Site/Views::subscription/emails/admin_success_html.php' );
     $text = \Dsc\System::instance()->get( 'theme' )->renderView( 'Striper/Site/Views::subscription/emails/admin_success_text.php' );
            
     \Dsc\System::instance()->get('mailer')->send('chris@ammonitenetworks.com', 'Successfully Subscribed', array($html, $text) );
    	 
    }
    
}