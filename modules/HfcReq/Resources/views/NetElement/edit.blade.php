{{--

@param $headline: the link header description in HTML

@param $form_path: the form view to be displayed inside this blade (mostly Generic.edit)
@param $route_name: the base route name of this object class which will be added

--}}

@include ('Generic.edit')


{{-- Reload when netelementtype_id field changes --}}
<script language="javascript">

$('#netelementtype_id').change(function() {
	location.href = location.href + "?&netelementtype_id=" + document.getElementById("netelementtype_id").options[document.getElementById("netelementtype_id").selectedIndex].value;
});

</script>
