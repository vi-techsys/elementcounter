<html>
    <head>
        <title>HTML Element Counter</title>
        <link rel="stylesheet" href="style/style.css">
    </head>
    <body>
        <div id="main">
            <?php
            session_start();
            include("class.crud.php");
            $domainID =base64_decode($_GET['dID']);
            $domain =base64_decode($_GET['name']);
            $cc = base64_decode($_GET['CC']);
            if($_SESSION['cfr_token']!=$cc){
                http_response_code(404);
                die("Permission required");
            }
            $crud =new CRUD();
            $elementRecords = $crud->customSelect('select e.id, e.name, (select sum(count) from request where domain_id = '.$domainID.' AND element_id = e.id) as total, (select sum(count) from request where element_id = e.id) as sumTotal from element e inner join request r on e.id = r.element_id where r.domain_id = '.$domainID.' group by e.id order by e.name asc;','all')
            ?>
            <h2>Element Statistics for <?= $domain?></h2>
            <a href="url_stat.php" class="link">Back</a>
            <table>
                <thead>
                    <th>S/N</th>
                    <th>Element</th>
                    <th>Total <br> (from this domain)</th>
                    <th>Sum Total <br> (from all requests)</th>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                    foreach($elementRecords as $r){?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $r['name']?></td>
                            <td><?= $r['total'] ?></td>
                            <td><?= $r['sumTotal'] ?></td>
                        </tr>

                    <?php $i++; }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
    </html>