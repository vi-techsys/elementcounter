<html>
    <head>
        <title>HTML Element Counter</title>
        <link rel="stylesheet" href="style/style.css">
    </head>
    <body>
        <div id="main">
            <?php
            session_start();
            $cfr_token = rand(100000,999999);
            $_SESSION['cfr_token'] = $cfr_token;
            include("class.crud.php");
            $crud =new CRUD();
            $domainRecords = $crud->customSelect('select d.id, d.name, count(r.url_id) as num, (select sum(duration)/24 from request where TIMESTAMPDIFF(HOUR, time, NOW())<=24 AND domain_id = d.id) as avg from domain d inner join request r on d.id = r.domain_id group by d.id order by d.name asc','all')
            ?>
            <h2>Domain Statistics</h2>
            <a href="index.php" class="link">Back</a>
            <table>
                <thead>
                    <th>S/N</th>
                    <th>Domain</th>
                    <th>Number of URLs</th>
                    <th>Avg. Fetch Time</th>
                    <th>Element Stat</th>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                    foreach($domainRecords as $dR){?>
                        <tr>
                        <td><?= $i ?></td>
                            <td><?= $dR['name']?></td>
                            <td><?= $dR['num']?></td>
                            <td><?=number_format($dR['avg'],0) . 'ms'?></td>
                            <td><a class="link" href="element_stat.php?dID=<?=base64_encode($dR['id']) ?>&name=<?=base64_encode($dR['name']) ?>&CC=<?= base64_encode($cfr_token) ?>">View</a></td>
                        </tr>

                    <?php $i++; }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
    </html>