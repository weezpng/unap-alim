@if(session()->has('toast-title'))
<script>
  $(document).Toasts('create', {
  title: "{{ Session('toast-title')}}",
  subtitle: "{{ Session('toast-subtitle')}}",
  body: "{{ Session('toast-text')}}",
  icon: "{{ Session('toast-icon')}}",
  autohide: true,
  autoremove: true,
  delay: 5000,
  class: "toast-not",
})
</script>
@endif
@if(Auth::check() && $CHANGE_MEAL_TIMES)
  <script>
    function changeRefTime(){
      var data = $("#refTimesForm").serializeArray();
      $.ajax({
          url: "{{route('gestão.horario.save')}}",
          type: "POST",
          data: {
              "_token": "{{ csrf_token() }}",
              data: data,
          },
          success: function(response) {
             $("#mealTime").modal('hide');
              if (response) {
                  if (response != 'success') {
                      document.getElementById("errorAddingTitle").innerHTML = "Erro";
                      document.getElementById("errorAddingText").innerHTML = response;
                      $("#errorAddingModal").modal()
                  } else {
                    $(document).Toasts('create', {
                      title: "Criado",
                      subtitle: "",
                      body: "O hórario de refeições foi alterado com sucesso!",
                      icon: "fa-solid fa-clock",
                      autohide: true,
                      autoremove: true,
                      delay: 3500,
                      class: "toast-not",
                    });
                    $("#mealTimeForms").load(location.href + " " + "#mealTimeForms");
                  }
              }
          }
      });
    }
  </script>
@endif
@if(Auth::check() && ($GET_STATS_NOMINAL))
  <script>
  $('#ExportQuantDate').on('apply.daterangepicker', function(ev, picker) {
      if (picker.startDate.format('YYYY-MM-DD')!=null && picker.endDate.format('YYYY-MM-DD')!=null) {
        $("#exporterBtn2").css("display", "block");
        $("#export_quant_date").val(picker.startDate.format('YYYY-MM-DD'));
      }
   });

    $('#ExportDate').on('apply.daterangepicker', function(ev, picker) {
        if (picker.startDate.format('YYYY-MM-DD')!=null && picker.endDate.format('YYYY-MM-DD')!=null) {
          $("#exporterBtn").css("display", "block");
          $("#export_date").val(picker.startDate.format('YYYY-MM-DD'));
        }
     });

     $('#ExportDateGeneral').on('apply.daterangepicker', function(ev, picker) {
         if (picker.startDate.format('YYYY-MM-DD')!=null && picker.endDate.format('YYYY-MM-DD')!=null) {
           $("#exporterBtnGeneral").css("display", "block");
           $("#export_date_general").val(picker.startDate.format('YYYY-MM-DD')+"|"+picker.endDate.format('YYYY-MM-DD'));
         }
      });

    function prepareExportModal(){
      $('#ExportDate').daterangepicker({
        format: 'DD/MM/YYYY',
        maxDate: moment(),
        startDate: moment(),
        showDropdowns: false,
        timePicker: false,
        opens: 'center',
        singleDatePicker: true,
        showRangeInputsOnCustomRangeOnly: false,
        applyClass: 'rangePickerApplyBtn',
        cancelClass: 'rangePickerCancelBtn',
        locale: {
          cancelLabel: 'Limpar',
          applyLabel: 'Aplicar',
          fromLabel: 'DE',
          toLabel: 'ATÉ',
          "daysOfWeek": [
              "D",
              "S",
              "T",
              "Q",
              "Q",
              "S",
              "S"
          ],
          monthNames: [
              "Janeiro",
              "Fevereiro",
              "Março",
              "Abril",
              "Maio",
              "Junho",
              "Julho",
              "Agosto",
              "Setembro",
              "Outubro",
              "Novembro",
              "Dezembro"
          ],
          firstDay: 1
        }
      },
    );

    $('#ExportDateGeneral').daterangepicker({
      format: 'DD/MM/YYYY',
      maxDate: moment(),
      startDate: moment(),
      showDropdowns: false,
      timePicker: false,
      opens: 'center',
      singleDatePicker: false,
      showRangeInputsOnCustomRangeOnly: false,
      applyClass: 'rangePickerApplyBtn',
      cancelClass: 'rangePickerCancelBtn',
      locale: {
        cancelLabel: 'Limpar',
        applyLabel: 'Aplicar',
        fromLabel: 'DE',
        toLabel: 'ATÉ',
        "daysOfWeek": [
            "D",
            "S",
            "T",
            "Q",
            "Q",
            "S",
            "S"
        ],
        monthNames: [
            "Janeiro",
            "Fevereiro",
            "Março",
            "Abril",
            "Maio",
            "Junho",
            "Julho",
            "Agosto",
            "Setembro",
            "Outubro",
            "Novembro",
            "Dezembro"
        ],
        firstDay: 1
      }
    },
  );

    $('#exportReportOverlay').removeClass( "overlay" );
    $('#exportReportOverlay').hide();
    $('#exportReportOverlayGeneral').removeClass( "overlay" );
    $('#exportReportOverlayGeneral').hide();
    $('#exporMonthReportOverlay').removeClass( "overlay" );
    $('#exporMonthReportOverlay').hide();
    $('#exportReportOverlay2').removeClass( "overlay" );
    $('#exportReportOverlay2').hide();

    $('#ExportQuantDate').daterangepicker({
      format: 'DD/MM/YYYY',
      maxDate: moment(),
      startDate: moment(),
      showDropdowns: false,
      timePicker: false,
      opens: 'center',
      singleDatePicker: true,
      showRangeInputsOnCustomRangeOnly: false,
      applyClass: 'rangePickerApplyBtn',
      cancelClass: 'rangePickerCancelBtn',
      locale: {
        cancelLabel: 'Limpar',
        applyLabel: 'Aplicar',
        fromLabel: 'DE',
        toLabel: 'ATÉ',
        "daysOfWeek": [
            "D",
            "S",
            "T",
            "Q",
            "Q",
            "S",
            "S"
        ],
        monthNames: [
            "Janeiro",
            "Fevereiro",
            "Março",
            "Abril",
            "Maio",
            "Junho",
            "Julho",
            "Agosto",
            "Setembro",
            "Outubro",
            "Novembro",
            "Dezembro"
        ],
        firstDay: 1
        }
      },
    );

  }

  function DownloadReportGeneral(){
    var post_data = $("#exportReportFormGeneral").serialize()
    $('#exportReportOverlayGeneral').addClass( "overlay" );
    $('#exportReportOverlayGeneral').show()
    $.ajax({
        url: "{{route('gestão.estatisticas.export.general')}}",
        type: "POST",
        data: post_data,
        xhrFields: {
          responseType: 'blob'
        },
        success: function(response){
          $('#exportReportOverlayGeneral').removeClass( "overlay" );
          $('#exportReportOverlayGeneral').hide()
          var title = "EXPORT_TOTAL("+$("#export_date_general").val()+").xlsx";
          var blob = new Blob([response],
            {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'});
            if (blob.size<=20) {
              $("#exportReportCloseGeneral").trigger('click');
              document.getElementById("errorAddingTitle").innerHTML = "Gerar relatório";
              document.getElementById("errorAddingText").innerHTML = "Impossivel gerar relátorio.<br>Não há marcações efectuadas para os critérios que escolheu.";
              $("#errorAddingModal").modal()
              return;
            } else {
              var link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = title;
              link.click();
              $("#exportReportCloseGeneral").trigger('click');
            }
        }
      });
  }

  function DownloadReport(){
    var post_data = $("#exportReportForm").serialize()
    $('#exportReportOverlay').addClass( "overlay" );
    $('#exportReportOverlay').show()
    $.ajax({
        url: "{{route('gestão.estatisticas.export.total')}}",
        type: "POST",
        data: post_data,
        xhrFields: {
          responseType: 'blob'
        },
        success: function(response){
          $('#exportReportOverlay').removeClass( "overlay" );
          $('#exportReportOverlay').hide()
          var title = "EXPORT_TOTAL_UTILIZADORES("+$("#export_date").val()+").xlsx";
          var blob = new Blob([response],
            {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'});
            if (blob.size<=20) {
              $("#exportReportClose").trigger('click');
              document.getElementById("errorAddingTitle").innerHTML = "Gerar relatório";
              document.getElementById("errorAddingText").innerHTML = "Impossivel gerar relátorio.<br>Não há marcações efectuadas para os critérios que escolheu.";
              $("#errorAddingModal").modal()
              return;
            } else {
              var link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = title;
              link.click();
              $("#exportReportClose").trigger('click');
            }
        }
      });
  }

  function DownloadQuantReport(){
    var post_data = $("#exportQuantReportForm").serialize()
    $('#exportReportOverlay2').addClass( "overlay" );
    $('#exportReportOverlay2').show()
    $.ajax({
        url: "{{route('gestão.estatisticas.export.quant')}}",
        type: "POST",
        data: post_data,
        xhrFields: {
          responseType: 'blob'
        },
        success: function(response){
          $('#exportReportOverlay2').removeClass( "overlay" );
          $('#exportReportOverlay2').hide()
          var title = "EXPORT_TOTAL_QUANTITATIVOS("+$("#export_quant_date").val()+").xlsx";
          var blob = new Blob([response],
            {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'});
            if (blob.size<=20) {
              $("#exportReportClose2").trigger('click');
              document.getElementById("errorAddingTitle").innerHTML = "Gerar relatório";
              document.getElementById("errorAddingText").innerHTML = "Impossivel gerar relátorio.<br>Não há marcações efectuadas para os critérios que escolheu.";
              $("#errorAddingModal").modal()
              return;
            } else {
              var link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = title;
              link.click();
              $("#exportReportClose2").trigger('click');
            }
        }
      });
  }

  function ShowExportBtn(){
    $("#exporterMonthBtn").css("display", "block");
  }

    function DownloadMontlyReport(){
      var post_data = $("#exportMontlhyReportForm").serialize()
      $('#exporMonthReportOverlay').addClass( "overlay" );
      $('#exporMonthReportOverlay').show()
      $.ajax({
        url: '{{ route("gestão.estatisticas.export.monthly") }}',
        type: "POST",
        data: post_data,
        xhrFields: {
          responseType: 'blob'
        },
        success: function(response){
          $('#exporMonthReportOverlay').removeClass( "overlay" );
          $('#exporMonthReportOverlay').hide()
          var title = "EXPORT_MENSAL_UTILIZADORES("+$("#month_select").val()+").xlsx";
          var blob = new Blob([response],
            {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'});
            if (blob.size<=20) {
              $("#exportMontlhyReportClose").trigger('click');
              document.getElementById("errorAddingTitle").innerHTML = "Gerar relatório";
              document.getElementById("errorAddingText").innerHTML = "Impossivel gerar relátorio.<br>Não há marcações efectuadas para os critérios que escolheu.";
              $("#errorAddingModal").modal()
              return;
            } else {
              var link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = title;
              link.click();
              $("#exportMontlhyReportClose").trigger('click');
            }
        }
      })
    }

    function DownloadMontlyReportMesses(){
      var post_data = $("#exportMontlhyReportFormMesses").serialize()
      $('#exporMonthReportOverlayMesses').addClass( "overlay" );
      $('#exporMonthReportOverlayMesses').show()
      $.ajax({
        url: '{{ route("gestão.estatisticas.export.monthly.messes") }}',
        type: "POST",
        data: post_data,
        xhrFields: {
          responseType: 'blob'
        },
        success: function(response){
          $('#exporMonthReportOverlayMesses').removeClass( "overlay" );
          $('#exporMonthReportOverlayMesses').hide()
          var title = "EXPORT_MENSAL_MESSE("+$("#month_select").val()+").xlsx";
          var blob = new Blob([response],
            {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'});
            if (blob.size<=20) {
              $("#exportMontlhyReportClose").trigger('click');
              document.getElementById("errorAddingTitle").innerHTML = "Gerar relatório";
              document.getElementById("errorAddingText").innerHTML = "Impossivel gerar relátorio.<br>Não há marcações efectuadas para os critérios que escolheu.";
              $("#errorAddingModal").modal()
              return;
            } else {
              var link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = title;
              link.click();
              $("#exportMontlhyReportCloseMesses").trigger('click');
            }
        }
      })
    }
  </script>

@elseif (Auth::check() && Auth::user()->user_permission=="MESSES")
  <script type="text/javascript">

    function prepareExportModal(){
      $('#exporMonthReportOverlayMesses').removeClass( "overlay" );
      $('#exporMonthReportOverlayMesses').hide();

    }

    function ShowExportBtnMesses(){
      $("#exporterMonthBtnMesses").css("display", "block");
    }

    function DownloadMontlyReportMesses(){
      var post_data = $("#exportMontlhyReportFormMesses").serialize()
      $('#exporMonthReportOverlayMesses').addClass( "overlay" );
      $('#exporMonthReportOverlayMesses').show()
      $.ajax({
        url: '{{ route("gestão.estatisticas.export.monthly.messes") }}',
        type: "POST",
        data: post_data,
        xhrFields: {
          responseType: 'blob'
        },
        success: function(response){
          $('#exporMonthReportOverlayMesses').removeClass( "overlay" );
          $('#exporMonthReportOverlayMesses').hide()
          var title = "EXPORT_MENSAL_UTILIZADORES("+$("#month_select").val()+").xlsx";
          var blob = new Blob([response],
            {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,'});
            if (blob.size<=20) {
              $("#exportMontlhyReportClose").trigger('click');
              document.getElementById("errorAddingTitle").innerHTML = "Gerar relatório";
              document.getElementById("errorAddingText").innerHTML = "Impossivel gerar relátorio.<br>Não há marcações efectuadas para os critérios que escolheu.";
              $("#errorAddingModal").modal()
              return;
            } else {
              var link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = title;
              link.click();
              $("#exportMontlhyReportCloseMesses").trigger('click');
            }
        }
      })
    }
  </script>
@endif
<script>
  function toggleDarkMode(cb) {
    var toggle_to = cb.checked;
    $.ajax({
        type: "POST",
        url: "{{route('profile.dark_mode.toggle')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            enable: toggle_to
        },
        success: function (msg) {
            if (msg != 'success') {
              alert(msg);
            } else {
              location.reload(true);
            }
        }
    });
  }
  function toggleCompactMode(cb) {
    var toggle_to = cb.checked;
    $.ajax({
        type: "POST",
        url: "{{route('profile.compact_mode.toggle')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            enable: toggle_to
        },
        success: function (msg) {
            if (msg != 'success') {
              alert(msg);
            } else {
              location.reload(true);
            }
        }
    });
  }
  function toggleFlatMode(cb){
    var toggle_to = cb.checked;
    $.ajax({
        type: "POST",
        url: "{{route('profile.flat_mode.toggle')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            enable: toggle_to
        },
        success: function (msg) {
            if (msg != 'success') {
              alert(msg);
            } else {
              location.reload(true);
            }
        }
    });
  }
  function toggleIcons(cb){
    var toggle_to = cb.checked;
    $.ajax({
        type: "POST",
        url: "{{route('profile.icons.toggle')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            enable: toggle_to
        },
        success: function (msg) {
            if (msg != 'success') {
              alert(msg);
            } else {
              location.reload(true);
            }
        }
    });
  }
  function toggleLiteMode(cb) {
    var toggle_to = cb.checked;
    $.ajax({
        type: "POST",
        url: "{{route('profile.lite_mode.toggle')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            enable: toggle_to
        },
        success: function (msg) {
            if (msg != 'success') {
              alert(msg);
            } else {
              location.reload(true);
            }
        }
    });
  }
  function toggleAutoCollapseMode(cb) {
    var toggle_to = cb.checked;
    $.ajax({
        type: "POST",
        url: "{{route('profile.auto_collapse.toggle')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            enable: toggle_to
        },
        success: function (msg) {
          if (msg != 'success') {
              alert(msg);
            } else {
              location.reload(true);
            }
        }
    });
  }
  function toggleStickyTop(cb) {
    var toggle_to = cb.checked;
    $.ajax({
        type: "POST",
        url: "{{route('profile.sticky_top.toggle')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            enable: toggle_to
        },
        success: function (msg) {
            if (msg != 'success') {
              alert(msg);
            } else {
              location.reload(true);
            }
        }
    });
  }
  function toggleResizeBox(cb) {
    var toggle_to = cb.checked;
    $.ajax({
        type: "POST",
        url: "{{route('profile.resize_box.toggle')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            enable: toggle_to
        },
        success: function (msg) {
            if (msg != 'success') {
              alert(msg);
            } else {
              location.reload(true);
            }
        }
    });
  }
  $(document).on('click','.dropdown-item-notification-href',function(){
      let id = $(this).attr('data-id');
      let title = $(this).attr('data-title');
      let text = $(this).attr('data-text');
      let action_data_id = $(this).attr('data-action-id');
      let action_data_type = $(this).attr('data-action-type');
       if (action_data_type!=null) {
         $("#notification-has-action").css("display", "block");
         $("#no-action").css("display", "none");
         if (action_data_type=="ASSOCIATION") {
           $("#actionAcceptActionID").val(action_data_id);
           $("#actionDeclineActionID").val(action_data_id);
           $("#has-action-accept-btn").attr("action", "{{ route('profile.association.by_user.confirm') }}")
           $("#has-action-decline-btn").attr("action", "{{ route('profile.association.by_user.decline') }}")
         }
       } else {
         $("#notificationID").val(id);
         $("#notification-has-action").css("display", "none");
         $("#no-action").css("display", "block");
       }
       $('#uniqueNotificationTitle').text(title);
       $('#uniqueNotificationText').text(text);
  });
</script>
<script>
  $(".no-action-close-btn").submit(function(e) {
    $('#notitificationModal').modal('toggle');
    e.preventDefault();
     var form = $(this);
    $.ajax({
     type: "POST",
     url: '{{ route('notifications.check.seen') }}',
     data: form.serialize(),
     success: function(data)
     {
       if (data.includes("NOTIFICATION")){
         document.getElementById(data).remove();
         var numberNotifications = document.getElementById("howManyNots").innerText;
         document.getElementById("howManyNots").innerText = Number(numberNotifications) - 1;
      }
     }
   });
  });
</script>
@if(Auth::check())
  <script>
    function delNotToUser(id){
      var divID = "#not"+id;
      $.ajax({
        type: "POST",
        url: "{{route('notifications.del.to_user')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            notID: id,
        },
        success: function (msg) {
            if (msg != 'success') {
              console.log(msg);
            } else {
              $(divID).remove();
              $("#howManyNots").html($("#howManyNots").html() - 1);
            }
        }
    });
    }
  </script>
@endif
@if (Auth::check())
  <script>
  @if (Auth::user()->resize_box=='Y')
    hideScroller(false);
  @else
    if ((window.innerHeight * 1.25) < document.documentElement.scrollHeight) {
      hideScroller(false);
      window.addEventListener("scroll", (event) => {
          let scroll = this.scrollY;
          if (scroll>500) {
            showScroller(true);
          } else {
            hideScroller(true);
          }
        });
      } else {
          hideScroller(false);
      }
    @endif
    function showScroller(effects){
      if (!$("#scroll_to_top").is(":visible")) {
        if (effects==true) {
          $( "#scroll_to_top" ).removeClass("slide-bck-bottom").css({display: "block"}).delay(400).addClass("slide-in-blurred-top");
        } else {
          $("#scroll_to_top").css({display: "block"});
        }
      }
    }
    function hideScroller(effects){
      if ($("#scroll_to_top").is(":visible")) {
        if (effects==true) {
          $( "#scroll_to_top" ).removeClass("slide-in-blurred-top").css({display: "none"}).delay(400).addClass("slide-bck-bottom");
        } else {
          $("#scroll_to_top").css({display: "none"});
        }
      }
    }
    function ScrollToTopSmooth(){
      window.scroll({top: 0, behavior: "smooth"})
    }
  </script>
@endif
<script>
@if(Auth::check() && Auth::user()->lite_mode=='Y')
  $(window).on('load', function(){
    setTimeout(removeLoader, 0);
    @if((Auth::check() && $GET_STATS_NOMINAL) || (Auth::check() && Auth::user()->user_permission=="MESSES"))
      prepareExportModal();
    @endif
  });
  function removeLoader(){
    $( "#loading" ).remove();
  }
@else
  $(window).on('load', function(){
    setTimeout(removeLoader, 500);
    @if((Auth::check() && $GET_STATS_NOMINAL) || (Auth::check() && Auth::user()->user_permission=="MESSES"))
      prepareExportModal();
    @endif
  });
  function removeLoader(){
    $("#water").addClass("scale-out-center").delay(500).queue(function(){
      $( "#loading" ).fadeOut(350, function() {
          jQuery('.card').each(function() {
              $(this).addClass('slide-in-blurred-top');
          });
          jQuery('.btn').each(function() {
            $(this).addClass('slide-in-blurred-top');
          });
          jQuery('.info-box').each(function() {
              $(this).addClass('slide-in-blurred-top');
          });
          jQuery('.error-page').each(function() {
              $(this).addClass('slide-in-blurred-top');
          });
          $( "#loading" ).remove();
        });
    });
  }
  $('[data-dismiss="modal"]').click(function(e){
      e.preventDefault();
      e.stopPropagation();
      var div = $(this).parent("div");
      while (true) {
        if (!$(div).hasClass("modal")) {
          div = $(div).parent();
        } else {
          break;
        }
      }
      var modalID = "#"+$(div).attr("id");
      $( modalID ).addClass("puff-out-center").delay(400).queue(function(){
          $(modalID).removeClass("puff-out-center");
      });
    setTimeout(function(){
        $(modalID).removeClass("puff-out-center");
        $(modalID).modal('hide');
      }, 450);
  });
@endif
</script>
@if(Auth::check())
  @if($VIEW_ALL_MEMBERS || $ACCEPT_NEW_MEMBERS || $DELETE_MEMBERS || $BLOCK_MEMBERS || $RESET_ACCOUNTS || $EDIT_MEMBERS )
  <script>
    $('#closeSearchBtn').on('click', function(){
      $("#searchResults").addClass("hide");
    })
  </script>

  <script>
  function QuickSearchAct(){
    if (event.keyCode === 27){
      $('#searchResults_table').empty();
      $("#searchResults").addClass("hide");
      $("#navBarSearch").val("");
   }
  }
  function SearcNav(control){
    $value=$("#navBarSearch").val();
    if (!$value) {
      $("#searchResults").addClass("hide");
      return false;
    }
    if(!isNaN($value)){
      $.ajax({
          type : 'get',
          url : "{{route('NIM.search')}}",
          data:{
            'search': $value
          },
        success:function(data){
          try {
            $('#searchResults_table').empty();
          var trHTML = '';
          $.each(data, function (i, item) {
            trHTML += '<tr style="border-top: 0px;">'
                + '<td style="width: 5.5rem;border-top: 0px; white-space: nowrap; text-align: right; padding-right:1.25rem !important;"><img style="height: 4rem; width:4rem; border-radius: 50%;object-fit: cover;object-position: top;" src="'+item.pic+'"></td>'
                + '<td style="border-top: 0px; white-space: nowrap;" id="searchBarResultName" name="searchBarResultName"><a href="https://10.102.21.45/alim/user/'
                + item.id + '">' + item.name  + '</a><br><span style="font-size: .7rem">'+item.posto+'</span></td> <td style="border-top: 0px; white-space: nowrap; width: 8rem; font-size: 1rem; padding-left: 2rem !important;" id="searchBarUnidade" name="searchBarUnidade">' + item.unidade + '</td>'
                + '<td style="border-top: 0px; white-space: nowrap; text-align: right; padding-right:1.25rem !important;  width:78rem; font-size: 0.8rem;" id="searchBarUsrtype" name="searchBarUsrtype">' + item.user_type + '</td></tr>';
            });
            $('#searchResults_table').append(trHTML);
            $("#searchResults").removeClass("hide");
          } catch (error) {
            $("#searchResults").addClass("hide");
            $('#searchResults_table').empty();
          }
        }
      });
    } else {
      $.ajax({
          type : 'get',
          url : "{{route('NAME.search')}}",
          data:{
            'search': $value
          },
        success:function(data){
          try {
            $('#searchResults_table').empty();
          var trHTML = '';
          $.each(data, function (i, item) {


            trHTML += '<tr style="border-top: 0px;">'
                + '<td style="width: 5.5rem;border-top: 0px; white-space: nowrap; text-align: right; padding-right:1.25rem !important;"><img style="height: 4rem; width:4rem; border-radius: 50%;object-fit: cover;object-position: top;" src="'+item.pic+'"></td>'
                + '<td style="border-top: 0px; white-space: nowrap; " id="searchBarResultName" name="searchBarResultName"><a href="https://10.102.21.45/alim/user/'
                + item.id + '">' + item.name  + '</a><br><span style="font-size: .7rem">'+item.posto+'</span></td> <td style="border-top: 0px; white-space: nowrap; width: 8rem; font-size: 1rem; padding-left: 2rem !important;" id="searchBarUnidade" name="searchBarUnidade">' + item.unidade + '</td>'
                + '<td style="border-top: 0px; white-space: nowrap; text-align: right; padding-right:1.25rem !important;width: 7rem; font-size: 0.8rem;" id="searchBarUsrtype" name="searchBarUsrtype">' + item.user_type + '</td></tr>';
            });
            $('#searchResults_table').append(trHTML);
            $("#searchResults").removeClass("hide");
          } catch (error) {
            $("#searchResults").addClass("hide");
            $('#searchResults_table').empty();
          }
        }
      });
    }
  }
  </script>
  @elseif((auth()->user()->user_permission=='MESSES'))
  <script>
    $('#closeSearchBtn').on('click', function(){
      $("#searchResults").addClass("hide");
    })
  </script>
  <script>
  function QuickSearchAct(){
    if (event.keyCode === 27){
      $('#searchResults_table').empty();
      $("#searchResults").addClass("hide");
      $("#navBarSearch").val("");
   }
  }
  function SearcNav(control){
    $value=$("#navBarSearch").val();
    if (!$value) {
      $("#searchResults").addClass("hide");
      return false;
    }
    if(!isNaN($value)){
      $.ajax({
          type : 'get',
          url : "{{route('HOSPEDE.QUARTO.search')}}",
          data:{
            'search': $value
          },
        success:function(data){
          try {
            $('#searchResults_table').empty();
          var trHTML = '';
          $.each(data, function (i, item) {
            trHTML += '<tr style="border-top: 0px;">'
                + '<td style="padding-left: 1.4rem !important; padding-right: 1.7rem !important; border-top: 0px; white-space: nowrap;" id="searchBarResultName" name="searchBarResultName"><a href="http://10.102.21.45/alim/gest%C3%A3o/h%C3%B3spedes/'
                + item.id + '">' + item.name  + '</a><br><span style="font-size: .7rem">'+item.type+'</span></td> <td style="border-top: 0px; white-space: nowrap; width: 8rem; font-size: 1rem; padding-left: 2rem !important;" id="searchBarUnidade" name="searchBarUnidade">' + item.type2 + '</td>'
                + '<td style="border-top: 0px; white-space: nowrap; text-align: right; padding-right:1.25rem !important;  width:78rem; font-size: 0.8rem;" id="searchBarUsrtype" name="searchBarUsrtype"> Contacto: <b>' + item.contacto + '</b></td></tr>';
            });
            $('#searchResults_table').append(trHTML);
            $("#searchResults").removeClass("hide");
          } catch (error) {
            $("#searchResults").addClass("hide");
            $('#searchResults_table').empty();
          }
        }
      });
    } else {
      $.ajax({
          type : 'get',
          url : "{{route('HOSPEDE.NAME.search')}}",
          data:{
            'search': $value
          },
        success:function(data){
          try {
            $('#searchResults_table').empty();
          var trHTML = '';
          $.each(data, function (i, item) {
            trHTML += '<tr style="border-top: 0px;">'
                + '<td style="padding-left: 1.4rem !important; padding-right: 1.7rem !important; border-top: 0px; white-space: nowrap;" id="searchBarResultName" name="searchBarResultName"><a href="http://10.102.21.45/alim/gest%C3%A3o/h%C3%B3spedes/'
                + item.id + '">' + item.name  + '</a><br><span style="font-size: .7rem">'+item.type+'</span></td> <td style="border-top: 0px; white-space: nowrap; width: 8rem; font-size: 1rem; padding-left: 2rem !important;" id="searchBarUnidade" name="searchBarUnidade">' + item.type2 + '</td>'
                + '<td style="border-top: 0px; white-space: nowrap; text-align: right; padding-right:1.25rem !important;  width:78rem; font-size: 0.8rem;" id="searchBarUsrtype" name="searchBarUsrtype"> Contacto: <b>' + item.contacto + '</b></td></tr>';
            });
            $('#searchResults_table').append(trHTML);
            $("#searchResults").removeClass("hide");
          } catch (error) {
            $("#searchResults").addClass("hide");
            $('#searchResults_table').empty();
          }
        }
      });
    }

  }
  </script>
  @endif
@else
<script>
  function toggle_dark_noauth(){
    $.ajax({
        type: "POST",
        url: "{{route('noauth.dark_mode.toggle')}}",
        async: true,
        data: {
            "_token": "{{ csrf_token() }}",
            enable: "toggle",
        },
        success: function (msg) {
            if (msg != 'success') {
              alert(msg);
            } else {
              location.reload(true);
            }
        }
    });
  }
</script>
@endif
