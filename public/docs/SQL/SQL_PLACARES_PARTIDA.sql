/* PLACARES PARTIDA */

select	concat(a.aposta_placar_mandante, ' - ', a.aposta_placar_visitante) as placar,
			count(*) as apostas
from		partida p
			inner join aposta a on p.partida_id = a.partida_id
where		p.partida_id = 45
group by concat(a.aposta_placar_mandante, ' - ', a.aposta_placar_visitante) 
order by count(*) desc