<?php
/**
 * Accounts - Users Profile
 *
 * @package Coordinator\Modules\Accounts
 * @author  Manuel Zavatta <manuel.zavatta@gmail.com>
 * @link    http://www.zavynet.org
 */
 // include module template
 require_once(MODULE_PATH."template.inc.php");
 // check permissions
 /** @todo check permissions */
 // set html title
 $html->setTitle(api_text("users_edit"));
 // get objects
 $user=new User($_REQUEST['idUser']);
 if(!$user->id){api_alerts_add(api_text("settings_alert_userNotFound"),"danger");api_redirect("?mod=settings&scr=users_list");}
 // build user description list
 $dl=new DescriptionList("br","dl-horizontal");
 $dl->addElement($user->fullname,api_image($user->avatar,"img-thumbnail",128));
 $dl->addElement(api_text("users_edit-mail"),$user->mail);
 $dl->addElement(api_text("users_edit-localization"),$localization->available_localizations[$user->localization]);
 $dl->addElement(api_text("users_edit-timezone"),$user->timezone);
 // build grid object
 $grid=new Grid();
 $grid->addRow();
 $grid->addCol($dl->render(),"col-xs-12");
 // add content to html
 $html->addContent($grid->render(FALSE));
 // renderize html page
 $html->render();
 // debug
 if($GLOBALS['debug']){api_dump($user,"user");}
?>