@isset($audit->report)
{!! $audit->report !!}
@else
  <p>No report was found.</p>
@endisset
