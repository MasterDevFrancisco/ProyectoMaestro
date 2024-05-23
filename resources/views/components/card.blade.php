@props(['cardTitle'=>'','cardTool'=>'','cardFooter'=>''])

<div class="card">
    <div class="card-header">
        <h3 class="card-title">{{$cardTitle}}</h3>
        <div class="card-tools">
            {{$cardTools}}
        </div>
    </div>
    <div class="card body">
        {{$slot}}
    </div>
    <div class="card-footer">
        {{$cardFooter}}
    </div>
</div>