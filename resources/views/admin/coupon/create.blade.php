 <!-- Modal -->
 <div class="modal fade" id="default-example-modal" tabindex="-1" role="dialog" aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h4 class="modal-title">
                 <i class="fal fa-gift"></i>
                    Coupons
                     <small class="m-0 text-muted">
                     </small>
                 </h4>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true"><i class="fal fa-times"></i></span>
                 </button>
             </div>
             <div class="modal-body">
             <form id="catForm">
            
                  <div class="form-group">
                         <label class="col-md-4 control-label">Code</label>
                         <div class="col-md-12">
                             <input type="text" class="form-control" name="code" id="code" placeholder="Code">
                             <input type="hidden" value="" id="coupon_id">
                            </div>
                         <span class="help-block"></span>
                     </div>
                     <div class="form-group">
                         <label class="col-md-4 control-label">Total Users</label>
                         <div class="col-md-12">
                             <input type="text" class="form-control" name="total_users" id="total_users" placeholder="Total Users">
                            </div>
                         <span class="help-block"></span>
                     </div>
                     <div class="form-group">
                         <label class="col-md-4 control-label">Expiry Date</label>
                         <div class="col-md-12">
                             <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="Expiry Date">
                            </div>
                         <span class="help-block"></span>
                     </div>
                     <div class="form-group">
                         <label class="col-md-4 control-label">Type</label>
                         <div class="col-md-12">
                             <select class="form-control" name="type" id="type">
                                 <option value="flat">Flat</option>
                                 <option value="per">Percentage</option>
                             </select>
                         </div>
                         <span class="help-block"></span>
                     </div>
                     <div class="form-group">
                         <label class="col-md-4 control-label">Type Value</label>
                         <div class="col-md-12">
                             <input type="text" class="form-control" name="type_value" id="type_value" placeholder="Type Value">
                            </div>
                         <span class="help-block"></span>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                         <button type="submit" id="save" class="btn btn-primary">Save</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 </div>