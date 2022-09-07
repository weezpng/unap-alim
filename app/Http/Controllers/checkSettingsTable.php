<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\helpdesk_settings;

/**
 * Verificar várias definições da plataforma
  *
 * Para cada ação é devolvido um bool ou um int.
 */
class checkSettingsTable extends Controller
{
    /**
     * Acede à tabela e devolve a entrada da definição em parâmetro.
     *
     * @param string $slug Slug da permissão armazenada em base de dados.
     *
     * @return helpdesk_settings
     */
    public function ACCESS_TABLE($settingSlug){
        return helpdesk_settings::where('settingSlug', $settingSlug)->first();
    }


  /**
   * Devolve o valor da definição [int/bool]
   *
   * @param string $result Grupo de permissões do utilizador
   *
   * @return mixed
   */
    public function RETURN_RESULT($result){
      if (is_int($result)) {
        return $result;
      } else {
        if ($result=='YES') {
          return true;
        } else {
          return false;
        }
      }
    }

  /**
   * Quantos dias de antecedência são necessários (no mínimo) para REMOVER uma marcação.
   *
   * @return int Valor definido
   */
    public function REMOVEMAX(){
      $table = $this::ACCESS_TABLE('RemoveMax');
      return $this::RETURN_RESULT($table->settingToggleInt);
    }

    /**
     * Quantos dias de antecedência são necessários (no mínimo) para EFECTUAR uma marcação.
     *
     * @return int Valor definido
     */
    public function ADDMAX(){
      $table = $this::ACCESS_TABLE('AddMax');
      return $this::RETURN_RESULT($table->settingToggleInt);
    }

    /**
     * Quantos militares se encontram de serviço num dia de semana, no Quartel da Serra do Pilar.
     *
     * @return int Valor definido
     */
    public function SvcSemanaQSP(){
      $table = $this::ACCESS_TABLE('SvcSemanaQSP');
      return $this::RETURN_RESULT($table->settingToggleInt);
    }

    /**
     * Quantos militares se encontram de serviço num dia de fim-de-semana, no Quartel da Serra do Pilar.
     *
     * @return int Valor definido
     */
    public function SvcFDSemanaQSP(){
      $table = $this::ACCESS_TABLE('SvcFDSemanaQSP');
      return $this::RETURN_RESULT($table->settingToggleInt);
    }

    /**
     * Quantos militares se encontram de serviço num dia de semana, no Quartel de Santo Ovídeo.
     *
     * @return int Valor definido
     */
    public function SvcSemanaQSO(){
      $table = $this::ACCESS_TABLE('SvcSemanaQSO');
      return $this::RETURN_RESULT($table->settingToggleInt);
    }

     /**
     * Quantos militares se encontram de serviço num dia de fim-de-semana,, no Quartel de Santo Ovídeo.
     *
     * @return int Valor definido
     */
    public function SvcFDSemanaQSO(){
      $table = $this::ACCESS_TABLE('SvcFDSemanaQSO');
      return $this::RETURN_RESULT($table->settingToggleInt);
    }
}
