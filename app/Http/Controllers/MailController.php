<?php
    namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Mail;

// Envoit de mail
class SendMailController extends Controller {
    private $mailToSend = array();

	public function __construct() {}

    public function sendEmail() {
        SendMailController::findUsers();
        //SendMailController::send($this->mailToSend);
    }

    private function findUsers() {
        /*
        Pour chaque balance dans hive_weight FAIRE
            Si id actuel = un id du tableau ALORS
                appartient a quelqu'un
                SI champs coché ALORS
                    Envoit mail

        Si chaque user qui a une balance a coché la case alors
    */
        // Table Hive Weight
        $tableHiveWeight = DB::select('SELECT * FROM hive_weights'); // Tout
            $numbersOfElementsTableHiveWeight = count($tableHiveWeight);

        // Table Scale User
        $tableScaleUser = DB::select('SELECT * FROM scale_user'); // Tout
        $numbersOfElementsTableScaleUser = count($tableScaleUser);

        // Table User
        $tableUsers = DB::select('SELECT * FROM users');
        $numbersOfElementsTableUsers = count($tableUsers);

        // Table des reports
        $tableScaleReports =  DB::select('SELECT * FROM scale_reports');

        //Pour toutes les balances de la table hive_weight)
        for($i = 0; $i < $numbersOfElementsTableHiveWeight; $i++) {
            // Pour tous les enregistrements de la table scale_user ( table de correspondance )
            for($j = 0; $j < $numbersOfElementsTableScaleUser; $j++) {

                 //SI ID similaire entre les deux tables ALORS ils sont propriétaires
                 if($tableHiveWeight[$i]->id === $tableScaleUser[$j]->scale_id) {

                    // SI le champs mail à était coché par les propriétaires ALORS on récupère leurs balances
                    if ($tableHiveWeight[$i]->mail_input === 1) {

                            // Tous les champs de la table users
                            foreach ($tableUsers as $users) {

                                // Si id des propriétaires ayant coché OUI est égal à celui de la table users
                                if($tableScaleUser[$j]->user_id === $users->id) {
                                   $mailToSend[] = $users->email.'<br>';

                                   // Selection de la dernière requete
                                   foreach($tableScaleReports as $report) {

                                       // SI id de la balance sont les mêmes que ceux de la table scale_reports ALORS
                                        if($tableScaleUser[$j]->scale_id === $report->scale_id) {
                                           echo $report->at.'<br>';

                                            //print_r($tableScaleReports[0]);

                                            /* Ne sais pas si je dois envoyer tous les jours la derniere meme c'est la même et qu'elle a été envoyé la veille
                                                    Tout le temps la derniere requete à envoyer (8h)

                                               Où dois je limiter le non envoit de mail à l'admin?
                                                    Tout ce qui est 1 dans la table role_user est admin et 2 pour l'utilisateur
                                                        Griser case? Enlever?
                                                        Empecher en php?
                                                Creer page d'administration des mails?
                                                    Creer page dans Admin qui permet de gérer les parametres de mails

                                                Comment eclater l'algo correctement?


                                                Comment traiter la derniere requete en php?
                                                    comparer avec la date actuelle?

                                                   compter nombre d'id

                                            */


                                            //if($report->scale_id)
                                            //print_r($report->at.'<br>');
                                            //echo $report->at.'<br>';
                                            //$today = date("Y-m-d H:i:s");
                                            //echo($today.'<br>');

                                            // foreach ($reports as $rep) {}
                                            //$dates = DB::select("SELECT MAX(at) FROM scale_reports WHERE scale_id = '$reports->scale_id'");
                                            //print_r($dates[0]);
                                        }
                                    }
                                }
                                //print_r($tableScaleUser[$j]->scale_id );
                           }
                    }
                }
            }
        }

            // Ajoute les mails à envoyer à l'instance
            $this->mailToSend = $mailToSend;

            $data = [
                'data' => 'chat',
                'password' => 'chien'
            ];
            // Récupère derniere requete par rapport à l'id des propriétaires ayant coché
          //  print_r($reports);

        }

    private function send($mail) {
        print_r($mail);

        try {/*
            // Envoit à la vue mail
            // http://127.0.0.1:8000/mes-balances/mail
            Mail::send('mail',["data1"=>$data] , function($message) {
                $enTete= "Requête du ";
                $datetime = date("Y-m-d H:i");
                $subject = $enTete.$datetime;

                $message->from('tony@sbeeh.io');
                $message->to('tonybengue@hotmail.fr')->subject($subject);
            });*/
            echo "Les emails sont envoyés, vérifiez les!<br>";
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
?>
