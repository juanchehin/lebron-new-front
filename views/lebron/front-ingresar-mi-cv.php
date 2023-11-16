<div class="text-center" hidden>

    <h2>Ingresar mi CV</h2>

</div>

    <div class="aa-contact-address-left">

        <h2>Ingresar mi Curriculum</h2>

        <p>Ingresa tu currículum vítae. Esto se almacenara en nuestra base de datos para Recursos Humanos.</p>

        <form class="comments-form" id="cv-form" method="post" action="!FrontInicio/cv" autocomplete="off" enctype="multipart/form-data">

            <div class="row">
                
                <div class="col-md-6 form-group">
                    Apellidos : 
                    <input type="text" name="apellidos" value="" class="form-control" placeholder="Apellidos..." required>

                </div>

                <div class="col-md-6 form-group">
                    Nombres : 
                    <input type="text" name="nombres" value="" class="form-control" placeholder="Nombres..." required>
    
                </div>
            
                <div class="col-md-6 form-group">
                    Email :
                    <input type="email" name="email" value="" class="form-control" placeholder="Ingres&aacute; tu E-mail" required>
            
                </div>
            
                <div class="col-md-6 form-group">
                    Telefono :
                    <input type="tel" class="form-control" name="telefono" placeholder="Teléfono (Whatsapp)">
            
                </div>

                <div class="col-md-6 form-group">
                    Domicilio :
                    <input type="domicilio" class="form-control" name="domicilio" placeholder="Domicilio">
            
                </div>

                <div class="col-md-6 form-group">
                    DNI :
                    <input type="dni" class="form-control" name="dni" placeholder="DNI...">
            
                </div>

                <div class="col-md-6 form-group">
                    Puesto deseado :
                    <input type="puesto" class="form-control" name="puesto" placeholder="Puesto...">
            
                </div>
            
                <div class="col-md-6 form-group">
                    Curriculum (*.pdf) :
                    <input class="form-control form-file" type="file" id="curriculum" name="curriculum" size="22"><span class="input-group-btn">
                </div>

            </div>

            IMPORTANTE: No se recibirán archivos de más de 500 KB.
            
            <div class="form-group text-right">
            
                <button type="submit" class="btn-success btn">Enviar <i class="fa fa-envelope"></i></button>
            
            </div>

        </form>
    </div>