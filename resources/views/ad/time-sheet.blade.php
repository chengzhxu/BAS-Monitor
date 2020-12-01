<div class="form-group  ">
    <label for="title" class="col-sm-2  control-label">投放时段</label>
    <div class="col-sm-8">
        <div class="input-group">
            <div id="J_calenderWrapper" data-table="timeline">
                <table>
                    <thead></thead>
                    <tbody id="J_timedSheet" >

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="/js/TimeSheet.js?" type="text/javascript"></script>
<script src="/js/work_time.js?" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function () {
        //initialize the javascript
        load_work_time();
    });

</script>
