<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\helpdesk_permissions;

/**
 * Verificar grupo de permissões do utilizador
 * 
 * Verifica se o grupo de permissões do utilizador está autorizado a concluir ação com '$slug'.
 * Para cada ação é devolvido um boolean.
 */
class ActiveDirectoryController extends Controller
{
  /**
   * Acede à tabela e devolve a quem a permissão é aplicada para dada slug.
   * 
   * @param string $slug Slug da permissão armazenada em base de dados.
   * 
   * @return helpdesk_permissions
   */
  public function ACCESS_TABLE($slug){
    return helpdesk_permissions::where('permission_slug', $slug)->value('permission_apply_to');
  }

  /**
   * Devolve o resultado se o utilizador tem acesso à permissão.
   * 
   * @param mixed $user_permission_group Grupo de permissões do utilizador
   * @param mixed $permission Permissão que necessária para a acção a decorrer.
   * 
   * @return bool
   */

  public function RETURN_RESULT($user_permission_group, $permission){
    if (auth()->user()->user_type=="HELPDESK") return true; // HELPDESK NÃO DEPENDE DESTES GRUPOS DE PERMISSÃO
    elseif (auth()->user()->user_permission=="TUDO") return true; // UTILIZADORES COM ACESSO GERAL
    elseif (str_contains($permission, $user_permission_group)) return true;
    else return false;
  }

  /**
   * Permite a visualização dos utilizadores na aplicação.
   * 
   * @return RETURN_RESULT
   */
  public function VIEW_ALL_MEMBERS(){
    $table = $this::ACCESS_TABLE("VIEW_ALL_MEMBERS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite aceitar novos utilizadores na aplicação.
   * 
   * @return RETURN_RESULT
   */
  public function ACCEPT_NEW_MEMBERS(){
    $table = $this::ACCESS_TABLE("ACCEPT_NEW_MEMBERS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite remover utilizadores da aplicação.
   * 
   * @return RETURN_RESULT
   */
  public function DELETE_MEMBERS(){
    $table = $this::ACCESS_TABLE("DELETE_MEMBERS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite bloquear utilizadores.
   * 
   * @return RETURN_RESULT
   */
  public function BLOCK_MEMBERS(){
    $table = $this::ACCESS_TABLE("BLOCK_MEMBERS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite o reset de contas de utilizadores.
   * 
   * @return RETURN_RESULT
   */
  public function RESET_ACCOUNTS(){
    $table = $this::ACCESS_TABLE("RESET_ACCOUNTS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite editar o perfil de utilizadores.
   * 
   * @return RETURN_RESULT
   */
  public function EDIT_MEMBERS(){
    $table = $this::ACCESS_TABLE("EDIT_MEMBERS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite publicar ementas.
   * 
   * @return RETURN_RESULT
   */
  public function ADD_EMENTA(){
    $table = $this::ACCESS_TABLE("ADD_EMENTA");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a edição de ementas já publicadas.
   * 
   * @return RETURN_RESULT
   */
  public function EDIT_EMENTA(){
    $table = $this::ACCESS_TABLE("EDIT_EMENTA");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite visualizar estatísticas gerais de marcações.
   * 
   * @return RETURN_RESULT
   */
  public function VIEW_GENERAL_STATS(){
    $table = $this::ACCESS_TABLE("VIEW_GENERAL_STATS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

    /**
   * Permite visualizar estatísticas de marcações de outras unidades.
   * 
   * @return RETURN_RESULT
   */
  public function GET_STATS_OTHER_UNITS(){
    $table = $this::ACCESS_TABLE("GET_STATS_OTHER_UNITS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite remover marcações num espaço de tempo menor ao definido nas definições da plataforma.
   * 
   * @return RETURN_RESULT
   */
  public function SHORT_PERIOD_REMOVAL(){
    $table = $this::ACCESS_TABLE("SHORT_PERIOD_REMOVAL");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite fazer marcações num espaço de tempo menor ao definido nas definições da plataforma.
   * 
   * @return RETURN_RESULT
   */
  public function SHORT_PERIOD_TAGS(){
    $table = $this::ACCESS_TABLE("SHORT_PERIOD_TAGS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite adicionar marcações no próprio dia.
   * 
   * @return RETURN_RESULT
   */
  public function ZERO_PERIOD_TAGS(){
    $table = $this::ACCESS_TABLE("ZERO_PERIOD_TAGS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a criação de tokens de ativação rápida de conta.
   */
  public function EXPRESS_TOKEN_GENERATION(){
    $table = $this::ACCESS_TABLE("EXPRESS_TOKEN_GENERATION");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a criação de pedidos de refeições para locais externos.
   * 
   * @return RETURN_RESULT
   */
  public function MEALS_TO_EXTERNAL(){
    $table = $this::ACCESS_TABLE("MEALS_TO_EXTERNAL");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);

  }

  /**
   * Permite a confirmação de alteração de unidade de um utilizador.
   * 
   * @return RETURN_RESULT
   */
  public function CONFIRM_UNIT_CHANGE(){
    $table = $this::ACCESS_TABLE("CONFIRM_UNIT_CHANGE");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a consulta de estatísticas nomínais.
   * 
   * @return RETURN_RESULT
   */
  public function GET_STATS_NOMINAL(){
    $table = $this::ACCESS_TABLE("GET_STATS_NOMINAL");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a consulta da relação de refeições a serem faturadas VS consumidas a um utilizador.
   * 
   * @return RETURN_RESULT
   */
  public function GET_CIVILIANS_REPORT(){
    $table = $this::ACCESS_TABLE("GET_CIVILIANS_REPORT");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite marcar os utilizadores que necessitam de confirmação de refeições para faturação por um periodo de tempo.
   * 
   * @return RETURN_RESULT
   */
  public function USERS_NEED_FATUR(){
    $table = $this::ACCESS_TABLE("USERS_NEED_FATUR");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a criação e verificação de utilizadores via um ficheiro excel.
   * 
   * @return RETURN_RESULT
   */
  public function EXPRESS_MEMBERS_CHECK(){
    $table = $this::ACCESS_TABLE("EXPRESS_MEMBERS_CHECK");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a edição das datas limite de marcação de refeições.
   * 
   * @return RETURN_RESULT
   */
  public function EDIT_DEADLINES_TAG(){
    $table = $this::ACCESS_TABLE("EDIT_DEADLINES_TAG");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a edição das datas limite de remoção de marcações.
   * 
   * @return RETURN_RESULT
   */
  public function EDIT_DEADLINES_UNTAG(){
    $table = $this::ACCESS_TABLE("EDIT_DEADLINES_UNTAG");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

   /**
   * Permite a edição do numero de pessoal de serviço para os pedidos quantitativos automáticos.
   * 
   * @return RETURN_RESULT
   */
  public function EDIT_PESSOAL_SVC(){
    $table = $this::ACCESS_TABLE("EDIT_PESSOAL_SVC");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

   /**
   * Permite a criação e removação de entradas aos utilizadores de Convalescenças, férias e diligências.
   * 
   * @return RETURN_RESULT
   */
  public function SCHEDULE_USER_VACATIONS(){
    $table = $this::ACCESS_TABLE("SCHEDULE_USER_VACATIONS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a visualização dos dados registados pelo o quiosque de entrada de refeições.
   * 
   * @return RETURN_RESULT
   */
  public function VIEW_DATA_QUIOSQUE(){
    $table = $this::ACCESS_TABLE("VIEW_DATA_QUIOSQUE");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a geração de códigos QR para os utilizadores.
   * 
   * @return RETURN_RESULT
   */
  public function MASS_QR_GENERATE(){
    $table = $this::ACCESS_TABLE("MASS_QR_GENERATE");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a criação de avisos gerais na plataforma.
   * 
   * @return RETURN_RESULT
   */
  public function GENERAL_WARNING_CREATION(){
    $table = $this::ACCESS_TABLE("GENERAL_WARNING_CREATION");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a marcação de um utilizador para as refeições marcadas serem dieta.
   * 
   * @return RETURN_RESULT
   */
  public function TAG_USER_DIETAS(){
    $table = $this::ACCESS_TABLE("TAG_USER_DIETAS");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a edição, criação e eliminação dos locais de refeição.
   * 
   * @return RETURN_RESULT
   */
  public function CHANGE_LOCAIS_REF(){
    $table = $this::ACCESS_TABLE("CHANGE_LOCAIS_REF");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a edição, criação e eliminação das U/E/O.
   * 
   * @return RETURN_RESULT
   */
  public function CHANGE_UNIDADES_MAN(){
    $table = $this::ACCESS_TABLE("CHANGE_UNIDADES_MAN");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }

  /**
   * Permite a edição dos horários de refeição.
   * 
   * @return RETURN_RESULT
   */
  public function CHANGE_MEAL_TIMES(){
    $table = $this::ACCESS_TABLE("CHANGE_MEAL_TIMES");
    return $this::RETURN_RESULT(Auth::user()->user_permission, $table);
  }
}
