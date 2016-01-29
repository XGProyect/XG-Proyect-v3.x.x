## InjectionMode
Default installatin mode move your implementation file to replace old calculateAttack.    
However, in this way, your project has 2 same files and expecially each time OPBE lib is updated you need to copy that implementation.     
Instead, using injectionMode the only used OPBE's implementation stay inside OPBE.  

## Installation

1. Download and upload all [OPBE files](https://github.com/jstar88/opbe/archive/master.zip) where you prefer.   
   For example,if you upload to *ROOT_PATH/includes/libs/*, you should see something like *ROOT_PATH/includes/libs/opbe/index.php*.  
   (Remember to rename opbe-master to opbe)   
   Alternatively, you can open a terminal and do:

    ```
    cd ROOT_PATH/includes/libs/
    sudo git clone https://github.com/jstar88/opbe.git
    
    ```

2. Replace all code inside *ROOT_PATH/includes/classes/missions/calculateAttack.php* with:
    
    ```php
    <?php
        require_once( ROOT_PATH . 'includes'.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'opbe'.DIRECTORY_SEPARATOR.'implementations'.DIRECTORY_SEPARATOR.'2Moons'.DIRECTORY_SEPARATOR.'1_6_1_injectionMode'.DIRECTORY_SEPARATOR.'calculateAttack.php' );
    ?>
    ```
3. Updating:
    You can use filezilla to upload opbe lib.   
    Alternatively, you can open a terminal and do:
    ```
    cd ROOT_PATH/includes/libs/opbe
    sudo git pull
    
    ```
     