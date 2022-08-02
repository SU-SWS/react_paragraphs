import React from 'react';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogTitle from '@mui/material/DialogTitle';

export const FormDialog = ({open, title, onClose, ...props}) => {
  let submitButton = React.createRef();

  /**
   * Form submit handler.
   */
  const onFormSubmit = (e) => {
    e.preventDefault();
    onClose();
  };

  return (
    <Dialog
      maxWidth='xl'
      fullWidth
      open={open}
      aria-labelledby="max-width-dialog-title"
      style={{zIndex: 900}}
      onClose={() => submitButton.current.click()}
      classes={{paper: 'h-[80vh]'}}
      // Disable this since CKEditor & Media modals have fields that need to be
      // focusable.
      disableEnforceFocus
    >
      <DialogTitle
        classes={{root: 'bg-[#6b6b6b] text-white sticky top-0 z-[1000]'}}
      >
        {title}
      </DialogTitle>

      <DialogContent classes={{root: 'flex flex-col'}} sx={{p: false}}>
        <form onSubmit={onFormSubmit} className="flex flex-1 flex-col">
          <div className="grow">
            {props.children}
          </div>
          <DialogActions
            sx={{p: '15px'}}
            classes={{root: 'flex-none sticky bottom-0 bg-[#f5f5f2] z-10'}}
          >
            <input
              ref={submitButton}
              className="button button--primary"
              type="submit"
              value="Continue"/>
          </DialogActions>
        </form>
      </DialogContent>
    </Dialog>
  )
};
