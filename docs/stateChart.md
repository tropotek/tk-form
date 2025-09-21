# Tk Form State Chart


## Form State Chart

The following os for reference, show the form flow and the states it can be in.
I found it handy when I was writing the code.

```
   Create Form
       |
       |
 Add Form Fields
       |
       |
 Load Field Values
       |
       |
 if (isSubmitted) ----[YES]---------------
       |                                 |
       |                                 |
       |                     Load Request Into Field values
      [No]                               |
       |                                 |
       |                         Validate Field Data
       |                                 |
       |                                 |
   Render Form   <-----[YES]-----  if (hasErrors)
                                         |
                                         |
                                        [No]
                                         |
                                         |
                               Save Data To Storage    
                                         | 
                                         |
                               Redirect To Success Page
  
```





