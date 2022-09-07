<?php
namespace App\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;
use \App\Models\User;
use \App\Models\marcacaotable;
use \App\Models\notification_table;
use \App\Models\user_children_checked_meals;
use \App\Http\Controllers\ActiveDirectoryController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if(isset($_SESSION) && isset($_SESSION["intranet"]) && $_SESSION["intranet"]==true){
            config([
                'session.cookie' => 'gestao_de_refeicoes_intranet_session',
                'session.same_site' => 'none',
                'session.secure' => true,
                'session.expire_on_close' => true,
            ]);
        }

        date_default_timezone_set("Europe/Lisbon");
        view()->composer('*', function ($viewSession)
        {

            $route = \Route::getCurrentRoute();
            if ($route) {
              if (Auth::check())
              {
                  try
                  {
                    /**
                    * Bootstrap any application services.
                    * Contar refeições WHERE (NIM=USER) e WHERE (DATA>HOJE)
                    */
                      $count = marcacaotable::where('NIM', Auth::user()->id)
                          ->where('data_marcacao', '>=', date('Y-m-d'))
                          ->count();
                    // Contar CONFIRMAÇÕES WHERE (NIM=USER) e WHERE (DATA>HOJE)
                      $count_conf = user_children_checked_meals::where('user', Auth::user()->id)
                          ->where('data', '>=', date('Y-m-d'))
                          ->where('check', 'Y')
                          ->count();
                  }
                  catch(\Exception $e)
                  {
                      $count = 0;
                      $count_conf = 0;
                  }
                  /**
                  * OBTER VALORES TRUE/FALSE de app/Http/Controllers/ActiveDirectoryController.php
                  */
                  $VIEW_ALL_MEMBERS = ((new ActiveDirectoryController)->VIEW_ALL_MEMBERS());
                  $ACCEPT_NEW_MEMBERS = ((new ActiveDirectoryController)->ACCEPT_NEW_MEMBERS());
                  $DELETE_MEMBERS = ((new ActiveDirectoryController)->DELETE_MEMBERS());
                  $BLOCK_MEMBERS = ((new ActiveDirectoryController)->BLOCK_MEMBERS());
                  $RESET_ACCOUNTS = ((new ActiveDirectoryController)->RESET_ACCOUNTS());
                  $EDIT_MEMBERS = ((new ActiveDirectoryController)->EDIT_MEMBERS());
                  $ADD_EMENTA = ((new ActiveDirectoryController)->ADD_EMENTA());
                  $EDIT_EMENTA = ((new ActiveDirectoryController)->EDIT_EMENTA());
                  $VIEW_GENERAL_STATS = ((new ActiveDirectoryController)->VIEW_GENERAL_STATS());
                  $SHORT_PERIOD_REMOVAL = ((new ActiveDirectoryController)->SHORT_PERIOD_REMOVAL());
                  $SHORT_PERIOD_TAGS = ((new ActiveDirectoryController)->SHORT_PERIOD_TAGS());
                  $ZERO_PERIOD_TAGS = ((new ActiveDirectoryController)->ZERO_PERIOD_TAGS());
                  $EXPRESS_TOKEN_GENERATION = ((new ActiveDirectoryController)->EXPRESS_TOKEN_GENERATION());
                  $MEALS_TO_EXTERNAL = ((new ActiveDirectoryController)->MEALS_TO_EXTERNAL());
                  $CONFIRM_UNIT_CHANGE = ((new ActiveDirectoryController)->CONFIRM_UNIT_CHANGE());
                  $GET_STATS_OTHER_UNITS = ((new ActiveDirectoryController)->GET_STATS_OTHER_UNITS());
                  $GET_STATS_NOMINAL = ((new ActiveDirectoryController)->GET_STATS_NOMINAL());
                  $GET_CIVILIANS_REPORT = ((new ActiveDirectoryController)->GET_CIVILIANS_REPORT());
                  $USERS_NEED_FATUR = ((new ActiveDirectoryController)->USERS_NEED_FATUR());
                  $EXPRESS_MEMBERS_CHECK = ((new ActiveDirectoryController)->EXPRESS_MEMBERS_CHECK());
                  $EDIT_DEADLINES_TAG = ((new ActiveDirectoryController)->EDIT_DEADLINES_TAG());
                  $EDIT_DEADLINES_UNTAG = ((new ActiveDirectoryController)->EDIT_DEADLINES_UNTAG());
                  $EDIT_PESSOAL_SVC = ((new ActiveDirectoryController)->EDIT_PESSOAL_SVC());
                  $SCHEDULE_USER_VACATIONS = ((new ActiveDirectoryController)->SCHEDULE_USER_VACATIONS());
                  $VIEW_DATA_QUIOSQUE = ((new ActiveDirectoryController)->VIEW_DATA_QUIOSQUE());
                  $MASS_QR_GENERATE = ((new ActiveDirectoryController)->MASS_QR_GENERATE());
                  $GENERAL_WARNING_CREATION = ((new ActiveDirectoryController)->GENERAL_WARNING_CREATION());
                  $TAG_USER_DIETAS = ((new ActiveDirectoryController)->TAG_USER_DIETAS());
                  $CHANGE_LOCAIS_REF = ((new ActiveDirectoryController)->CHANGE_LOCAIS_REF());
                  $CHANGE_UNIDADES_MAN = ((new ActiveDirectoryController)->CHANGE_UNIDADES_MAN());
                  $CHANGE_MEAL_TIMES = ((new ActiveDirectoryController)->CHANGE_MEAL_TIMES());
                  $_MARCACOES_DINHEIRO = (Auth::user()->isTagOblig==null) ? false : true;
                  if (\Route::getCurrentRoute()->uri != "perfil/settings/save")
                  {
                    /**
                    * Troca pendente sem indicar USER substituição, abortar troca de unidade
                    */
                      $trocarUnidadePendente = User::where('id', Auth::user()->id)
                          ->first()
                          ->value('trocarUnidade');
                      $trocaPendente = ($trocarUnidadePendente != null ? true : false);
                      if ($trocaPendente == true)
                      {
                          if (Auth::user()->user_type == "POC" || Auth::user()->user_type == "ADMIN")
                          {
                              if (Auth::user()->accountReplacementPOC == null)
                              {
                                  $user = User::where('id', Auth::user()->id)
                                      ->first();
                                  $try = $user->trocarUnidade;
                                  $try = \App\Models\unap_unidades::where('slug', $try)->value('name');
                                  $user->trocarUnidade = null;
                                  $user->save();
                                  $notifications = new \App\Http\Controllers\notificationsHandler;
                                  $notifications->new_notification( /*TITLE*/
                                  'Transferência de unidade', /*TEXT*/
                                  'O seu pedido de troca de unidade para a ' . $try . ' foi cancelada, porquê não chegou a ser indicado um utilizador para herdar as suas responsabilidades.',
                                  /*TYPE*/
                                  'WARNING', /*GERAL*/
                                  null, /*TO USER*/
                                  Auth::user()->id, /*CREATED BY*/
                                  'SYSTEM: ACCOUNT MOVE CANCELED @SYSTEM', null);
                              }
                          }
                      }
                  }
                  /**
                  * Apresentar VIEW com Permissões populadas
                  * e com contagem de marcações e confirmações (em sidebar)
                  */


                  $viewSession->with([
                    'VIEW_ALL_MEMBERS' => $VIEW_ALL_MEMBERS,
                    'ACCEPT_NEW_MEMBERS' => $ACCEPT_NEW_MEMBERS,
                    'DELETE_MEMBERS' => $DELETE_MEMBERS,
                    'BLOCK_MEMBERS' => $BLOCK_MEMBERS,
                    'RESET_ACCOUNTS' => $RESET_ACCOUNTS,
                    'EDIT_MEMBERS' => $EDIT_MEMBERS,
                    'ADD_EMENTA' => $ADD_EMENTA,
                    'EDIT_EMENTA' => $EDIT_EMENTA,
                    'VIEW_GENERAL_STATS' => $VIEW_GENERAL_STATS,
                    'SHORT_PERIOD_REMOVAL' => $SHORT_PERIOD_REMOVAL,
                    'SHORT_PERIOD_TAGS' => $SHORT_PERIOD_TAGS,
                    'ZERO_PERIOD_TAGS' => $ZERO_PERIOD_TAGS,
                    'EXPRESS_TOKEN_GENERATION' => $EXPRESS_TOKEN_GENERATION,
                    'MEALS_TO_EXTERNAL' => $MEALS_TO_EXTERNAL,
                    'CONFIRM_UNIT_CHANGE' => $CONFIRM_UNIT_CHANGE,
                    'GET_STATS_OTHER_UNITS' => $GET_STATS_OTHER_UNITS,
                    'GET_STATS_NOMINAL' => $GET_STATS_NOMINAL,
                    'GET_CIVILIANS_REPORT' => $GET_CIVILIANS_REPORT,
                    'USERS_NEED_FATUR' => $USERS_NEED_FATUR,
                    'EXPRESS_MEMBERS_CHECK' => $EXPRESS_MEMBERS_CHECK,
                    'EDIT_DEADLINES_TAG' => $EDIT_DEADLINES_TAG,
                    'EDIT_DEADLINES_UNTAG' => $EDIT_DEADLINES_UNTAG,
                    'EDIT_PESSOAL_SVC' => $EDIT_PESSOAL_SVC,
                    'SCHEDULE_USER_VACATIONS' => $SCHEDULE_USER_VACATIONS,
                    'VIEW_DATA_QUIOSQUE' => $VIEW_DATA_QUIOSQUE ,
                    'GENERAL_WARNING_CREATION' => $GENERAL_WARNING_CREATION,
                    'MASS_QR_GENERATE' => $MASS_QR_GENERATE,
                    'TAG_USER_DIETAS' => $TAG_USER_DIETAS,
                    'CHANGE_LOCAIS_REF' => $CHANGE_LOCAIS_REF,
                    'CHANGE_UNIDADES_MAN' => $CHANGE_UNIDADES_MAN,
                    'CHANGE_MEAL_TIMES' => $CHANGE_MEAL_TIMES,
                    'MARCACOES_A_DINHEIRO' => $_MARCACOES_DINHEIRO,
                    'howManyMarcacoes' => $count,
                    'howManyConf' => $count_conf,
                ]);
              }
              else
              {
                /**
                * Sem sessão iniciada, se nao for uma das rotas, redirecionar para INDEX
                */
                  $route = \Route::getCurrentRoute()->uri;
                  if ($route != "/" && $route != "register" && $route != "login"  && $route != "ementa")
                  {
                      return redirect()->route('index');
                  }
                  $viewSession->with([
                    'VIEW_ALL_MEMBERS' => false,
                    'ACCEPT_NEW_MEMBERS' => false,
                    'DELETE_MEMBERS' => false,
                    'BLOCK_MEMBERS' => false,
                    'RESET_ACCOUNTS' => false,
                    'EDIT_MEMBERS' => false,
                    'ADD_EMENTA' => false,
                    'EDIT_EMENTA' => false,
                    'VIEW_GENERAL_STATS' => false,
                    'SHORT_PERIOD_REMOVAL' => false,
                    'EXPRESS_TOKEN_GENERATION' => false,
                    'CONFIRM_UNIT_CHANGE' => false,
                    'GET_STATS_OTHER_UNITS' => false,
                    'GET_STATS_NOMINAL' => false,
                    'GET_CIVILIANS_REPORT' => false,
                    'USERS_NEED_FATUR' => false,
                    'EXPRESS_MEMBERS_CHECK' => false,
                    'EDIT_DEADLINES_TAG' => false,
                    'EDIT_DEADLINES_UNTAG' => false,
                    'EDIT_PESSOAL_SVC' => false,
                    'SCHEDULE_USER_VACATIONS' => false,
                    'VIEW_DATA_QUIOSQUE' => false ,
                    'GENERAL_WARNING_CREATION' => false,
                    'TAG_USER_DIETAS' => false,
                    'CHANGE_LOCAIS_REF' => false,
                    'CHANGE_UNIDADES_MAN' => false,
                    'MASS_QR_GENERATE' => false,
                    'MARCACOES_A_DINHEIRO' => false,
                    'howManyMarcacoes' => null,
                    'howManyConf' => null,
                    ]);
              }
            }
        });

        view()->composer('layout.footer', function ($footerView)
        {
          /**
          * Versão de aplicação para footer
          */
            $footerView->with('APP_VERSION', config('app.APP_VERSION'));
        });

        view()->composer('welcome', function ($welcomeView)
        {
          /**
          * Verificação de estado de conta para apresentar possiveis erros em INDEX
          */
            if (Auth::check())
            {
                if (Auth::user()->user_type == "USER" && auth()
                    ->user()->isAccountChildren == 'N') $needsParent = true;
                elseif (Auth::user()->user_type == "HELPDESK" && auth()
                    ->user()->isAccountChildren == 'N') $needsParent = true;
                else $needsParent = false;
                $msgs_index =  \App\Models\PlatformWarnings::where('to_show', 'INDEX')->get()->all();
                if (empty($msgs_index)) $msgs_index = null;
                $msgs_no_auth = null;
            }
            else
            {
                $msgs_no_auth =  \App\Models\PlatformWarnings::where('to_show', 'INDEX2')->get()->all();
                if (empty($msgs_no_auth)) $msgs_no_auth = null;
                $needsParent = false;
                $msgs_index = null;
            }

            $welcomeView->with([
                'needsParent' => $needsParent,
                'msgs'=> $msgs_index,
                'msgs_no_auth'=> $msgs_no_auth,
            ]);
        });

        view()->composer('layout.notificationsbar', function ($notificationsView)
        {

          /**
          * OBTER notificações para popular barra de notificações
          */
            try
            {
                $notificationPersonal = notification_table::where('lapses_at', '>', date('Y-m-d'))
                    ->orWhere('lapses_at', null)
                    ->where('notification_toUser', auth()->user()->id)
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('notification_type', 'DESC')
                    ->get()
                    ->all();
                $pendingAction = \App\Models\pending_actions::where('to_id', Auth::user()->id)
                    ->where('is_valid', 'Y')
                    ->first();
                foreach ($notificationPersonal as $key => $notificaton)
                {
                    $pendingAction = \App\Models\pending_actions::where('to_id', Auth::user()->id)
                        ->where('is_valid', 'Y')
                        ->where('notification_id', $notificaton->id)
                        ->first();
                    if ($pendingAction)
                    {
                        $notificationPersonal[$key]['action'] = $pendingAction;
                    }
                }
                $user_type = auth()->user()->user_type;
                if ($user_type=="ADMIN") $user_type = "ADMINS";
                elseif ($user_type=="POC") $user_type = "SUPERS";
                elseif ($user_type=="USER") $user_type = "USERS";
                $unidade = auth()->user()->unidade;
                $notificationGeneralUserGroup = notification_table::whereRaw('FIND_IN_SET(?, notification_geral)', $user_type)->where('lapses_at', '>', date('Y-m-d'))->orderBy('created_at', 'DESC')->get()->all();
                $notificationGeneralUserGroup2 = notification_table::whereRaw('FIND_IN_SET(?, notification_geral)', $user_type)->where('lapses_at',  null)->orderBy('created_at', 'DESC')->get()->all();
                $notificationGeneralUnidade = notification_table::whereRaw('FIND_IN_SET(?, notification_geral)', [$unidade])->where('lapses_at', '>', date('Y-m-d'))->orderBy('created_at', 'DESC')->get()->all();
                $notificationGeneralUnidade2 = notification_table::whereRaw('FIND_IN_SET(?, notification_geral)', [$unidade])->where('lapses_at', null)->orderBy('created_at', 'DESC')->get()->all();
                $notificationGeneralUserGroup = array_merge($notificationGeneralUserGroup, $notificationGeneralUserGroup2);
                $notificationGeneralUnidade = array_merge($notificationGeneralUnidade, $notificationGeneralUnidade2);
                $allGeralNotifications = array_merge($notificationGeneralUnidade, $notificationGeneralUserGroup);
                $allNotifications = array_merge($notificationPersonal, $allGeralNotifications);
                $created_At = array_column($allNotifications, 'created_at');
                array_multisort($created_At, SORT_DESC, $allNotifications);

                foreach ($allNotifications as $key => $not) {
                    $id_in_field = ';'.$not['id'].';';
                    if(str_contains(Auth::user()->dismissed_nots, $id_in_field)){
                        unset($allNotifications[$key]);
                    }
                }
                $notificationsView->with('notifications', $allNotifications);
            }
            catch(\Exception $e)
            {
                $notificationsView->with('notifications', '0');
            }
        });

        view()->composer('layout.sidebar', function ($sidebar)
        {
          try
          {
            # QUIOSQUE
            $today = date("Y-m-d"); $new_Date = date('Y-m-d', strtotime($today . ' -15 days'));

            #POC
            if ( Auth::user()->user_type=='POC') {
              $unidade_to_check = Auth::user()->unidade;
              if ($unidade_to_check=="UnAp/CmdPess" ||  $unidade_to_check=="UnAp/CmdPess/QSO") {
                $users = User::where('unidade', 'UnAp/CmdPess')->orWhere('unidade', 'UnAp/CmdPess/QSO');
              } elseif ($unidade_to_check=="MMBatalha" || $unidade_to_check=="MMAntas") {
                $users = User::where('unidade', 'MMBatalha')->orWhere('unidade', 'MMAntas');
              } else {
                $users = User::where('unidade', $unidade_to_check);
              }
              $users_POC = $users->where('lock', 'N')->where('account_verified', 'Y')->count();
            } else {
              $users_POC = '0';
            }



            $users_count = \App\Models\User::count();
            $new_users_count = (\App\Models\User::where('trocarUnidade', '!=', null)->count()) + (User::where('account_verified', 'N')->count());
            $minhas_entradas = \App\Models\entradasQuiosque::where('REGISTADO_DATE',  '>', $new_Date)->where('NIM', Auth::user()->id)->count();
            $units = \App\Models\unap_unidades::count();
            $localsref_on = \App\Models\locaisref::where('status', 'OK')->count();
            $localsref_off = \App\Models\locaisref::where('status', 'NOK')->count();

            $warnings_count = \App\Models\PlatformWarnings::count();

            $users_dieta = \App\Models\Dietas::where('data_inicio', '<=',  $today)->where('data_fim', '>=',  $today)->count();

            $users_ferias = \App\Models\Ferias::where('data_inicio', '<=',  $today)->where('data_fim', '>=',  $today)->count();

            $my_team = \App\Models\User::where('user_permission', Auth::user()->user_permission)->count();

            $team_posts = \App\Models\TeamPosts::where('posted_group', Auth::user()->user_permission)->count();

            $assc_usr = (\App\Models\User::where('accountChildrenOf', Auth::user()->id)->count())
                      + (\App\Models\User::where('account2ndChildrenOf', Auth::user()->id)->count())
                      + (\App\Models\users_children::where('parentNIM', Auth::user()->id)->count())
                      + (\App\Models\users_children::where('parent2nNIM', Auth::user()->id)->count());

            $sidebar->with([
              'assoc_user' => $assc_usr,
              'howManyUsers' => $users_count,
              'howManyNewUsers' => $new_users_count,
              'myEntranceQu' => $minhas_entradas,
              'POC_Users' => $users_POC,
              'units_count' => $units,
              'locals_on_count' => $localsref_on,
              'locals_off_count' => $localsref_off,
              'warnings_count' => $warnings_count,
              'users_ferias' => $users_ferias,
              'users_dieta' => $users_dieta,
              'team_members' => $my_team,
              'team_posts' => $team_posts,
            ]);

          } catch(\Exception $e)
            {
              $sidebar->with([
                'assoc_user' => '0',
                'howManyUsers' => '0',
                'howManyNewUsers' => '0',
                'myEntranceQu' => '0',
                'POC_Users' => '0',
                'units_count' => '0',
                'locals_on_count' => '0',
                'locals_off_count' => '0',
                'warnings_count' => '0',
                'users_ferias' => '0',
                'users_dieta' => '0',
                'team_members' => '0',
                'team_posts' => '0',
              ]);
            }
        });

        view()->composer('layout.navbar', function ($navBar)
        {
          /**
          * Contar notificações não vistas
          * E informação de PARTNER (accountPartnerPOC)
          */
            try
            {
                $notificationPersonal = notification_table::where('lapses_at', '>', date('Y-m-d'))
                    ->orWhere('lapses_at', null)
                    ->where('notification_toUser', auth()->user()->id)
                    ->get()
                    ->all();
                $user_type = auth()->user()->user_type;
                if ($user_type=="ADMIN") $user_type = "ADMINS";
                elseif ($user_type=="POC") $user_type = "SUPERS";
                elseif ($user_type=="USER") $user_type = "USERS";
                $unidade = auth()->user()->unidade;
                $notificationGeneralUserGroup = notification_table::whereRaw('FIND_IN_SET(?, notification_geral)', $user_type)->where('lapses_at', '>', date('Y-m-d'))->orderBy('created_at', 'DESC')->get()->all();
                $notificationGeneralUserGroup2 = notification_table::whereRaw('FIND_IN_SET(?, notification_geral)', $user_type)->where('lapses_at',  null)->orderBy('created_at', 'DESC')->get()->all();
                $notificationGeneralUnidade = notification_table::whereRaw('FIND_IN_SET(?, notification_geral)', [$unidade])->where('lapses_at', '>', date('Y-m-d'))->orderBy('created_at', 'DESC')->get()->all();
                $notificationGeneralUnidade2 = notification_table::whereRaw('FIND_IN_SET(?, notification_geral)', [$unidade])->where('lapses_at', null)->orderBy('created_at', 'DESC')->get()->all();
                $notificationGeneralUserGroup = array_merge($notificationGeneralUserGroup, $notificationGeneralUserGroup2);
                $notificationGeneralUnidade = array_merge($notificationGeneralUnidade, $notificationGeneralUnidade2);
                $allGeralNotifications = array_merge($notificationGeneralUnidade, $notificationGeneralUserGroup);
                $allNotifications = array_merge($notificationPersonal, $allGeralNotifications);
                $created_At = array_column($allNotifications, 'created_at');
                array_multisort($created_At, SORT_DESC, $allNotifications);
                $count = 0;
                foreach ($allNotifications as $key => $notification)
                {
                    if ($notification->notification_seen != "Y")
                    {
                        $id_in_field = ';'.$notification['id'].';';
                        if(!str_contains(Auth::user()->dismissed_nots, $id_in_field)){
                            $count++;
                        }
                    }
                }

                $NIM = Auth::user()->id;
                while ((strlen((string)$NIM)) < 8) {
                    $NIM = 0 . (string)$NIM;
                }

                $temp_count =Auth::user()->last_nots;

                if ($temp_count < $count) {
                    session()->put('toast-title', 'Novas notificações');
                    session()->put('toast-subtitle', null);
                    session()->put('toast-text', 'Há novas notificações a serem vistas.');
                    session()->put('toast-icon', 'fas fa-bell');
                    $user = User::where('id', $NIM)->first();
                    $user->last_nots = $count;
                    $user->save();
                } else {
                  if (session('dnf')=='Y') {
                    session()->put('dnf', 'N');
                  } else {
                    session()->forget(['toast-title', 'toast-subtitle', 'toast-text', 'toast-icon']);
                  }
                }

                $me = \App\Models\User::where('id', Auth::user()->id)->first();
                $me->last_nots = $count;
                $me->save();

                $partner_ID = Auth::user()->accountPartnerPOC;
                if ($partner_ID) {
                  $len = strlen((string)$partner_ID);
                  if ($len < 8) $partner_ID = 0 . (string)$partner_ID;
                  $partner = \App\Models\User::where('id', $partner_ID)->first();
                } else {
                  $partner = null;
                }

                $navBar->with(['howManyNotifications' => $count, 'partner' => $partner]);
            }
            catch(\Exception $e)
            {
                $navBar->with(['howManyNotifications' => '0', 'partner' => null]);
            }
        });

        view()->composer('layout.modals.changeRefModal', function ($TimeModal)
        {
          /**
          * Horário de refeição
          */

          $_1REF = \App\Models\HorariosRef::where('meal', '1REF')->first();
          $_2REF = \App\Models\HorariosRef::where('meal', '2REF')->first();
          $_3REF = \App\Models\HorariosRef::where('meal', '3REF')->first();
          $TimeModal->with([
            '_1REF' => $_1REF,
            '_2REF' => $_2REF,
            '_3REF' => $_3REF,
          ]);
      });
    }
}
