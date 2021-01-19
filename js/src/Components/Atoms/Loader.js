import React from 'react';
import CircularProgress from '@material-ui/core/CircularProgress';

export const Loader = () => {
  return (
    <div style={{position: 'absolute', left: 'calc(50% - 20px)', top: 'calc(50% - 20px)'}}>
      <CircularProgress/>
    </div>
  )
};
